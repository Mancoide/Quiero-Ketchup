<?php

namespace App\Models;

use App\Enums\LegalTextType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalText extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_legal_texts';

    protected $fillable = [
        'type',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'type' => LegalTextType::class,
        ];
    }
}
