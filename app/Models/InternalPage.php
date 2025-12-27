<?php

namespace App\Models;

use App\Enums\CmsStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_internal_pages';

    protected $fillable = [
        'section_id',
        'title',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CmsStatus::class,
        ];
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
