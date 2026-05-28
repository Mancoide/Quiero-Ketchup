<?php

namespace App\Models;

use App\Enums\ReconciliationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Reconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'matching_mode',
        'bank_file_path',
        'bank_file_name',
        'company_file_path',
        'company_file_name',
        'result_file',
        'total_bank_records',
        'total_company_records',
        'matched_records',
        'bank_only_records',
        'company_only_records',
        'possible_matches',
        'summary_payload',
        'total_reconciled_bank',
        'total_reconciled_company',
        'total_unreconciled_bank',
        'total_unreconciled_company',
        'ledger_balance',
        'outstanding_checks',
        'bank_unregistered_credits',
        'unbooked_debits',
        'unbooked_credits',
        'reconciled_balance',
        'bank_statement_balance',
        'difference_amount',
        'status',
        'processing_log',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReconciliationStatus::class,
            'summary_payload' => 'array',
            'total_reconciled_bank' => 'decimal:2',
            'total_reconciled_company' => 'decimal:2',
            'total_unreconciled_bank' => 'decimal:2',
            'total_unreconciled_company' => 'decimal:2',
            'ledger_balance' => 'decimal:2',
            'outstanding_checks' => 'decimal:2',
            'bank_unregistered_credits' => 'decimal:2',
            'unbooked_debits' => 'decimal:2',
            'unbooked_credits' => 'decimal:2',
            'reconciled_balance' => 'decimal:2',
            'bank_statement_balance' => 'decimal:2',
            'difference_amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getResultFileUrlAttribute(): ?string
    {
        if (blank($this->result_file)) {
            return null;
        }

        return Storage::disk('public')->url($this->result_file);
    }
}
