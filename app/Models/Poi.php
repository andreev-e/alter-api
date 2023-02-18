<?php

namespace App\Models;

use App\Jobs\PoiGeocodeJob;
use App\Models\Traits\ImageSizesTrait;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Poi extends Model implements HasMedia
{
    use InteractsWithMedia;
    use ImageSizesTrait {
        ImageSizesTrait::registerMediaConversions insteadof InteractsWithMedia;
    }
    public const FULL_SIZE = 1200;
    public const THUMB_SIZE = 600;

    protected $table = 'poi';

    protected $fillable = [
        'lat',
        'lng',
        'name',
        'description',
        'addon',
        'route',
        'route_o',
        'links',
        'type',
        'show',
        'author',
        'views',
        'views_month',
        'views_today',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'show'=> 'boolean',
    ];

    public static function boot()
    {
        parent::boot();
        self::created(function($poi) {
                PoiGeocodeJob::dispatch($poi);
        });
        self::updated(function($poi) {
            if ($poi->isDirty('lat') || $poi->isDirty('lng')) {
                PoiGeocodeJob::dispatch($poi);
            }
        });
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'relationship', 'POSTID', 'TAGID')
            ->where('TYPE', 0);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class)
            ->orderBy('count', 'desc');
    }

    public function getNearestAttribute()
    {
        return Cache::remember('nearest:' . $this->id, 24 * 60 * 60, function() {
            return self::query()
                ->select(DB::raw("*,
                111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(lat))
                     * COS(RADIANS($this->lat))
                     * COS(RADIANS(lng - $this->lng))
                     + SIN(RADIANS(lat))
                     * SIN(RADIANS($this->lat))))) AS 'dist'"))
                ->where('lat', '<>', 0)
                ->where('lng', '<>', 0)
                ->where('id', '<>', $this->id)
                ->havingRaw('dist IS NOT NULL')
                ->orderBy('dist')->limit(3)->get();
        });
    }

    public function twits(): HasMany
    {
        return $this->hasMany(Comment::class, 'backlink')
            ->where('approved', '=', 1)
            ->orderBy('time', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'username', 'author');
    }

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class);
    }

    public function getDefaultRelationsAttribute(): array
    {
        return ['locations', 'tags', 'user', 'routes'];
    }
}
