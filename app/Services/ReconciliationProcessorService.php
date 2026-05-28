<?php

namespace App\Services;

use App\Enums\ReconciliationStatus;
use App\Models\Reconciliation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ReconciliationProcessorService
{
    public function process(Reconciliation $reconciliation): Reconciliation
    {
        $reconciliation->update([
            'status' => ReconciliationStatus::PROCESSING,
            'error_message' => null,
            'processing_log' => null,
        ]);

        $bankPath = Storage::disk('local')->path($reconciliation->bank_file_path);
        $companyPath = Storage::disk('local')->path($reconciliation->company_file_path);
        $resultRelativePath = sprintf(
            'reconciliations/results/reconciliation_%s_%s.xlsx',
            $reconciliation->id,
            now()->format('Ymd_His')
        );
        $resultAbsolutePath = Storage::disk('public')->path($resultRelativePath);

        if (! is_dir(dirname($resultAbsolutePath))) {
            mkdir(dirname($resultAbsolutePath), 0755, true);
        }

        $command = [
            'python3',
            base_path('scripts/reconcile_transactions.py'),
            $bankPath,
            $companyPath,
            $resultAbsolutePath,
        ];

        Log::info('Processing reconciliation with legacy Python flow.', [
            'reconciliation_id' => $reconciliation->id,
            'command' => $command,
        ]);

        $process = new Process($command, base_path(), timeout: 300);

        try {
            $process->mustRun();

            $output = trim($process->getOutput());
            $payload = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
            $engineLog = trim($process->getErrorOutput());

            $updates = [
                'result_file' => $resultRelativePath,
                'total_bank_records' => (int) ($payload['total_bank_records'] ?? 0),
                'total_company_records' => (int) ($payload['total_company_records'] ?? 0),
                'matched_records' => (int) ($payload['matched_records'] ?? 0),
                'bank_only_records' => (int) ($payload['bank_only_records'] ?? 0),
                'company_only_records' => (int) ($payload['company_only_records'] ?? 0),
                'possible_matches' => (int) ($payload['possible_matches'] ?? 0),
                'summary_payload' => $payload['summary'] ?? null,
                'ledger_balance' => $payload['summary']['ledger_balance'] ?? null,
                'outstanding_checks' => $payload['summary']['outstanding_checks'] ?? null,
                'bank_unregistered_credits' => $payload['summary']['bank_unregistered_credits'] ?? null,
                'unbooked_debits' => $payload['summary']['unbooked_debits'] ?? null,
                'unbooked_credits' => $payload['summary']['unbooked_credits'] ?? null,
                'reconciled_balance' => $payload['summary']['reconciled_balance'] ?? null,
                'bank_statement_balance' => $payload['summary']['bank_statement_balance'] ?? null,
                'difference_amount' => $payload['summary']['difference'] ?? null,
                'status' => ReconciliationStatus::COMPLETED,
                'processing_log' => trim($engineLog . PHP_EOL . $output),
                'processed_at' => now(),
            ];

            // Permite procesar conciliaciones aunque la migracion nueva todavia no se haya ejecutado.
            // Los importes separados siguen estando disponibles dentro de summary_payload.
            $optionalSummaryColumns = [
                'total_reconciled_bank' => $payload['summary']['total_conciliado_banco'] ?? null,
                'total_reconciled_company' => $payload['summary']['total_conciliado_mayor'] ?? null,
                'total_unreconciled_bank' => $payload['summary']['total_no_conciliado_banco'] ?? null,
                'total_unreconciled_company' => $payload['summary']['total_no_conciliado_mayor'] ?? null,
            ];

            foreach ($optionalSummaryColumns as $column => $value) {
                if (Schema::hasColumn($reconciliation->getTable(), $column)) {
                    $updates[$column] = $value;
                }
            }

            $reconciliation->update($updates);

            Log::info('Reconciliation process completed.', [
                'reconciliation_id' => $reconciliation->id,
                'result_file' => $resultRelativePath,
                'total_mayor' => $payload['total_company_records'] ?? 0,
                'total_banco' => $payload['total_bank_records'] ?? 0,
                'total_conciliados' => $payload['matched_records'] ?? 0,
                'total_pendientes_mayor' => $payload['company_only_records'] ?? 0,
                'total_pendientes_banco' => $payload['bank_only_records'] ?? 0,
            ]);
        } catch (\Throwable $exception) {
            $message = $exception instanceof ProcessFailedException
                ? $exception->getProcess()->getErrorOutput()
                : $exception->getMessage();

            $reconciliation->update([
                'status' => ReconciliationStatus::FAILED,
                'error_message' => trim($message) ?: 'No se pudo procesar la conciliación.',
                'processing_log' => trim($process->getOutput() . PHP_EOL . $process->getErrorOutput()),
            ]);

            Log::error('Reconciliation process failed.', [
                'reconciliation_id' => $reconciliation->id,
                'message' => $exception->getMessage(),
            ]);

            throw new RuntimeException(
                trim($message) ?: 'No se pudo procesar la conciliación.',
                previous: $exception
            );
        }

        return $reconciliation->refresh();
    }
}
