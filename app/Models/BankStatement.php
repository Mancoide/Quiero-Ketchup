<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'bank_name',
        'account_number',
        'start_date',
        'end_date',
        'currency',
        'opening_balance',
        'closing_balance',
        'total_transactions',
        'extracted_at',
        'status', // pending, processing, completed, failed
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'extracted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function reconciliation()
    {
        return $this->hasOne(ReconciliationResult::class);
    }
}
