<?php

namespace App\Models;

use App\Models\Traits\ImageManualSortTrait;
use App\Models\Traits\ImageSizesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Route extends Model implements HasMedia
{
    use InteractsWithMedia;
    use ImageSizesTrait {
        ImageSizesTrait::registerMediaConversions insteadof InteractsWithMedia;
    }
    use ImageManualSortTrait;

    public const TYPE = 'route';
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
        'encoded_route',
        'start',
        'finish',
        'author',
        'show',
    ];

    protected $casts = [
        'show'=> 'boolean',
    ];

    public function pois(): BelongsToMany
    {
        return $this->belongsToMany(Poi::class);
    }

    public function twits(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->where('approved', '=', 1)
            ->orderBy('time', 'desc');
    }

    public function getDefaultRelationsAttribute(): array
    {
        return ['pois'];
    }
}
