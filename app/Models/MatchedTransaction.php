<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchedTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_transaction_id',
        'system_transaction_id',
        'reconciliation_result_id',
        'match_score', // 0-100 porcentaje de confianza
        'matched_at',
    ];

    protected $casts = [
        'matched_at' => 'datetime',
    ];

    public function bankTransaction()
    {
        return $this->belongsTo(BankTransaction::class);
    }

    public function systemTransaction()
    {
        return $this->belongsTo(SystemTransaction::class);
    }

    public function reconciliationResult()
    {
        return $this->belongsTo(ReconciliationResult::class);
    }
}
