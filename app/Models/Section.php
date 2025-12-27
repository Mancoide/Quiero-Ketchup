<?php

namespace App\Models;

use App\Enums\CmsStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_sections';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CmsStatus::class,
        ];
    }

    public function banners()
    {
        return $this->hasMany(Banner::class, 'section_id');
    }

    public function internalPages()
    {
        return $this->hasMany(InternalPage::class, 'section_id');
    }
}
