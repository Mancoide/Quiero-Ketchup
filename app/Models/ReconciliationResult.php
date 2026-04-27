<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconciliationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_statement_id',
        'reconciliation_file_id',
        'total_matched',
        'total_unmatched_bank',
        'total_unmatched_system',
        'discrepancy_amount',
        'match_percentage',
        'status', // completed, pending_review
        'processing_time', // en segundos
        'reconciled_at',
    ];

    protected $casts = [
        'discrepancy_amount' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    public function bankStatement()
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function reconciliationFile()
    {
        return $this->belongsTo(ReconciliationFile::class);
    }

    public function matchedTransactions()
    {
        return $this->hasMany(MatchedTransaction::class);
    }

    public function discrepancies()
    {
        return $this->hasMany(Discrepancy::class);
    }
}
