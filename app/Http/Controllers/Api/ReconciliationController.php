<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankStatement;
use App\Models\ReconciliationFile;
use App\Models\ReconciliationResult;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;

class ReconciliationController extends Controller
{
    protected $reconciliationService;

    public function __construct(ReconciliationService $reconciliationService)
    {
        $this->reconciliationService = $reconciliationService;
    }

    public function reconcile(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|exists:bank_statements,id',
            'reconciliation_file_id' => 'required|exists:reconciliation_files,id',
        ]);

        $bankStatement = BankStatement::findOrFail($request->bank_statement_id);
        $reconciliationFile = ReconciliationFile::findOrFail($request->reconciliation_file_id);

        // Actualizar estados a processing
        $bankStatement->update(['status' => 'processing']);
        $reconciliationFile->update(['status' => 'processing']);

        try {
            $result = $this->reconciliationService->reconcile($bankStatement, $reconciliationFile);

            // Marcar como completado
            $bankStatement->update(['status' => 'completed']);
            $reconciliationFile->update(['status' => 'completed']);

            return response()->json([
                'success' => true,
                'message' => 'Reconciliación completada',
                'result_id' => $result->id,
                'summary' => [
                    'total_matched' => $result->total_matched,
                    'total_unmatched_bank' => $result->total_unmatched_bank,
                    'total_unmatched_system' => $result->total_unmatched_system,
                    'match_percentage' => $result->match_percentage,
                    'discrepancy_amount' => $result->discrepancy_amount,
                    'processing_time' => $result->processing_time,
                ],
            ], 201);
        } catch (\Exception $e) {
            $bankStatement->update(['status' => 'failed']);
            $reconciliationFile->update(['status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => 'Error en la reconciliación: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getResult($resultId)
    {
        $result = ReconciliationResult::with([
            'matchedTransactions.bankTransaction',
            'matchedTransactions.systemTransaction',
            'discrepancies',
        ])->findOrFail($resultId);

        return response()->json([
            'success' => true,
            'result' => $result,
        ]);
    }
}
