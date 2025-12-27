<?php

namespace App\Models;

use App\Enums\CmsStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'cms_banners';

    protected $fillable = [
        'section_id',
        'button_text',
        'image_link',
        'button_link',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => CmsStatus::class,
            'sort_order' => 'integer',
        ];
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function getImageUrl(string $conversion = ''): string
    {
        return $this->getFirstMediaUrl('image', $conversion);
    }
}
