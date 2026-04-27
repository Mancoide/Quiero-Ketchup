<?php

namespace App\Services;

use App\Models\BankStatement;
use App\Models\BankTransaction;
use App\Models\ReconciliationFile;
use App\Models\ReconciliationResult;
use App\Models\SystemTransaction;
use App\Models\MatchedTransaction;
use App\Models\Discrepancy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;
use Carbon\Carbon;

class ReconciliationService
{
    public function reconcile(BankStatement $bankStatement, ReconciliationFile $reconciliationFile): ReconciliationResult
    {
        $startTime = microtime(true);

        // Parsear el extracto bancario (PDF)
        $bankTransactions = $this->parseBankStatement($bankStatement);
        $bankStatement->update([
            'total_transactions' => count($bankTransactions),
        ]);

        // Parsear el archivo de conciliación
        $systemTransactions = $this->parseReconciliationFile($reconciliationFile);
        $reconciliationFile->update([
            'total_items' => count($systemTransactions),
        ]);

        // Crear la reconciliación
        $result = ReconciliationResult::create([
            'bank_statement_id' => $bankStatement->id,
            'reconciliation_file_id' => $reconciliationFile->id,
            'status' => 'pending_review',
        ]);

        // Procesar coincidencias
        $matchingResults = $this->matchTransactions($bankTransactions, $systemTransactions);

        // Guardar coincidencias
        foreach ($matchingResults['matched'] as $match) {
            MatchedTransaction::create([
                'bank_transaction_id' => $match['bank_id'],
                'system_transaction_id' => $match['system_id'],
                'reconciliation_result_id' => $result->id,
                'match_score' => $match['score'],
                'matched_at' => now(),
            ]);
        }

        // Guardar discrepancias
        foreach ($matchingResults['discrepancies'] as $discrepancy) {
            Discrepancy::create([
                'reconciliation_result_id' => $result->id,
                'bank_transaction_id' => $discrepancy['bank_id'] ?? null,
                'system_transaction_id' => $discrepancy['system_id'] ?? null,
                'type' => $discrepancy['type'],
                'amount_difference' => $discrepancy['amount_difference'] ?? 0,
                'status' => 'pending',
            ]);
        }

        // Actualizar resultado
        $endTime = microtime(true);
        $processingTime = intval($endTime - $startTime);

        $totalMatched = count($matchingResults['matched']);
        $totalUnmatchedBank = count($matchingResults['unmatched_bank']);
        $totalUnmatchedSystem = count($matchingResults['unmatched_system']);
        $totalTransactions = $totalMatched + $totalUnmatchedBank + $totalUnmatchedSystem;

        $matchPercentage = $totalTransactions > 0 ? round(($totalMatched / $totalTransactions) * 100, 2) : 0;

        $result->update([
            'total_matched' => $totalMatched,
            'total_unmatched_bank' => $totalUnmatchedBank,
            'total_unmatched_system' => $totalUnmatchedSystem,
            'match_percentage' => $matchPercentage,
            'discrepancy_amount' => collect($matchingResults['discrepancies'])->sum('amount_difference'),
            'processing_time' => $processingTime,
            'reconciled_at' => now(),
            'status' => 'completed',
        ]);

        return $result;
    }

    private function parseBankStatement(BankStatement $statement): array
    {
        $filePath = Storage::disk('local')->path($statement->file_path);

        // Llamar al script de Python
        try {
            $result = Process::run([
                'python3',
                base_path('scripts/parse_pdf.py'),
                $filePath,
                'bank',
            ]);

            if ($result->successful()) {
                $transactions = json_decode($result->output(), true);
                return $this->saveBankTransactions($statement, $transactions);
            }
        } catch (\Exception $e) {
            \Log::error('Error parsing bank statement: ' . $e->getMessage());
        }

        return [];
    }

    private function parseReconciliationFile(ReconciliationFile $file): array
    {
        $filePath = Storage::disk('local')->path($file->file_path);
        $fileType = pathinfo($file->file_path, PATHINFO_EXTENSION);

        try {
            if ($fileType === 'pdf') {
                $result = Process::run([
                    'python3',
                    base_path('scripts/parse_pdf.py'),
                    $filePath,
                    'system',
                ]);
            } else {
                // Para CSV, XLSX
                $result = Process::run([
                    'python3',
                    base_path('scripts/parse_spreadsheet.py'),
                    $filePath,
                ]);
            }

            if ($result->successful()) {
                $transactions = json_decode($result->output(), true);
                return $this->saveSystemTransactions($file, $transactions);
            }
        } catch (\Exception $e) {
            \Log::error('Error parsing reconciliation file: ' . $e->getMessage());
        }

        return [];
    }

    private function saveBankTransactions(BankStatement $statement, array $transactions): array
    {
        $savedTransactions = [];

        foreach ($transactions as $transaction) {
            $bankTransaction = BankTransaction::create([
                'bank_statement_id' => $statement->id,
                'transaction_date' => Carbon::parse($transaction['date'] ?? now()),
                'amount' => $transaction['amount'] ?? 0,
                'description' => $transaction['description'] ?? '',
                'reference_number' => $transaction['reference'] ?? '',
                'type' => strtolower($transaction['type']) ?? 'debit',
                'balance_after' => $transaction['balance'] ?? 0,
            ]);

            $savedTransactions[] = $bankTransaction;
        }

        return $savedTransactions;
    }

    private function saveSystemTransactions(ReconciliationFile $file, array $transactions): array
    {
        $savedTransactions = [];

        foreach ($transactions as $transaction) {
            $systemTransaction = SystemTransaction::create([
                'reconciliation_file_id' => $file->id,
                'transaction_date' => Carbon::parse($transaction['date'] ?? now()),
                'amount' => $transaction['amount'] ?? 0,
                'description' => $transaction['description'] ?? '',
                'reference_number' => $transaction['reference'] ?? '',
                'type' => strtolower($transaction['type']) ?? 'debit',
            ]);

            $savedTransactions[] = $systemTransaction;
        }

        return $savedTransactions;
    }

    private function matchTransactions(array $bankTransactions, array $systemTransactions): array
    {
        $matched = [];
        $unmatched_bank = [];
        $unmatched_system = [];
        $discrepancies = [];

        $usedSystemIds = [];

        foreach ($bankTransactions as $bankTx) {
            $bestMatch = null;
            $bestScore = 0;

            foreach ($systemTransactions as $systemTx) {
                if (in_array($systemTx->id, $usedSystemIds)) {
                    continue;
                }

                // Calcular score de coincidencia
                $score = $this->calculateMatchScore($bankTx, $systemTx);

                if ($score > $bestScore && $score >= 75) {
                    $bestScore = $score;
                    $bestMatch = $systemTx;
                }
            }

            if ($bestMatch) {
                $matched[] = [
                    'bank_id' => $bankTx->id,
                    'system_id' => $bestMatch->id,
                    'score' => $bestScore,
                ];
                $usedSystemIds[] = $bestMatch->id;
            } else {
                $unmatched_bank[] = $bankTx->id;
                $discrepancies[] = [
                    'bank_id' => $bankTx->id,
                    'type' => 'only_in_bank',
                    'amount_difference' => $bankTx->amount,
                ];
            }
        }

        // Transacciones del sistema no emparejadas
        foreach ($systemTransactions as $systemTx) {
            if (!in_array($systemTx->id, $usedSystemIds)) {
                $unmatched_system[] = $systemTx->id;
                $discrepancies[] = [
                    'system_id' => $systemTx->id,
                    'type' => 'only_in_system',
                    'amount_difference' => $systemTx->amount,
                ];
            }
        }

        return [
            'matched' => $matched,
            'unmatched_bank' => $unmatched_bank,
            'unmatched_system' => $unmatched_system,
            'discrepancies' => $discrepancies,
        ];
    }

    private function calculateMatchScore($bankTx, $systemTx): float
    {
        $score = 0;

        // Comparar fecha (±2 días = 30 puntos)
        $dateDiff = abs($bankTx->transaction_date->diffInDays($systemTx->transaction_date));
        if ($dateDiff <= 2) {
            $score += 30;
        } elseif ($dateDiff <= 7) {
            $score += 15;
        }

        // Comparar monto (exacto = 50 puntos, ±5% = 30 puntos)
        $amountDiff = abs($bankTx->amount - $systemTx->amount);
        $percentDiff = ($amountDiff / max(abs($bankTx->amount), 1)) * 100;

        if ($amountDiff == 0) {
            $score += 50;
        } elseif ($percentDiff <= 5) {
            $score += 30;
        } elseif ($percentDiff <= 10) {
            $score += 15;
        }

        // Comparar descripción/referencia (similar = 20 puntos)
        $bankRef = strtolower($bankTx->reference_number . ' ' . $bankTx->description);
        $systemRef = strtolower($systemTx->reference_number . ' ' . $systemTx->description);

        similar_text($bankRef, $systemRef, $similarity);
        if ($similarity > 70) {
            $score += 20;
        } elseif ($similarity > 40) {
            $score += 10;
        }

        return $score;
    }
}
