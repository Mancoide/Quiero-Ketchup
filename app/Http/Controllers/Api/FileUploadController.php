<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankStatement;
use App\Models\ReconciliationFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function uploadBankStatement(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
        ]);

        $file = $request->file('file');
        $path = Storage::disk('local')->putFileAs(
            'bank-statements',
            $file,
            $file->getClientOriginalName()
        );

        $statement = BankStatement::create([
            'user_id' => auth()->id() ?? 1,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Extracto bancario cargado correctamente',
            'statement_id' => $statement->id,
        ], 201);
    }

    public function uploadReconciliationFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,xlsx,xls,csv',
            'file_type' => 'required|string',
        ]);

        $file = $request->file('file');
        $path = Storage::disk('local')->putFileAs(
            'reconciliation-files',
            $file,
            $file->getClientOriginalName()
        );

        $reconcFile = ReconciliationFile::create([
            'user_id' => auth()->id() ?? 1,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $request->file_type,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Archivo de conciliación cargado correctamente',
            'file_id' => $reconcFile->id,
        ], 201);
    }
}
