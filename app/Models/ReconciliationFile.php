<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReconciliationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_type', // internal_system_export
        'total_items',
        'parsed_at',
        'status', // pending, processing, completed, failed
    ];

    protected $casts = [
        'parsed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function systemTransactions()
    {
        return $this->hasMany(SystemTransaction::class);
    }

    public function reconciliation()
    {
        return $this->hasOne(ReconciliationResult::class);
    }
}
