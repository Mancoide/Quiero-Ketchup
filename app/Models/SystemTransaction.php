<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_file_id',
        'transaction_date',
        'amount',
        'description',
        'reference_number',
        'type', // debit, credit
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function reconciliationFile()
    {
        return $this->belongsTo(ReconciliationFile::class);
    }

    public function matchedTransactions()
    {
        return $this->hasMany(MatchedTransaction::class);
    }
}
