<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discrepancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_result_id',
        'bank_transaction_id',
        'system_transaction_id',
        'type', // only_in_bank, only_in_system, duplicate, amount_mismatch
        'amount_difference',
        'notes',
        'status', // pending, resolved, ignored
    ];

    protected $casts = [
        'amount_difference' => 'decimal:2',
    ];

    public function reconciliationResult()
    {
        return $this->belongsTo(ReconciliationResult::class);
    }

    public function bankTransaction()
    {
        return $this->belongsTo(BankTransaction::class);
    }

    public function systemTransaction()
    {
        return $this->belongsTo(SystemTransaction::class);
    }
}
