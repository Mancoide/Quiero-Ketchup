<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_statement_id',
        'transaction_date',
        'amount',
        'description',
        'reference_number',
        'type', // debit, credit
        'balance_after',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function bankStatement()
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function matchedTransactions()
    {
        return $this->hasMany(MatchedTransaction::class);
    }
}
