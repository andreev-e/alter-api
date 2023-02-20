<?php

namespace App\Models;

use App\Models\Traits\ImageSizesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Route extends Model implements HasMedia
{
    use InteractsWithMedia;
    use ImageSizesTrait {
        ImageSizesTrait::registerMediaConversions insteadof InteractsWithMedia;
    }

    public const FULL_SIZE = 1200;
    public const THUMB_SIZE = 600;
    public const TMP_MEDIA_FOLDER = 'tmp-img';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'cost',
        'days',
        'route',
        'links',
    ];

    public function pois(): BelongsToMany
    {
        return $this->belongsToMany(Poi::class);
    }

    public function getDefaultRelationsAttribute(): array
    {
        return ['pois'];
    }
}
