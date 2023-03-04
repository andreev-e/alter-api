<?php

namespace App\Models;

use App\Jobs\PoiGeocodeJob;
use App\Models\Traits\ImageManualSortTrait;
use App\Models\Traits\ImageSizesTrait;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Poi extends Model implements HasMedia
{
    use InteractsWithMedia;
    use ImageSizesTrait {
        ImageSizesTrait::registerMediaConversions insteadof InteractsWithMedia;
    }
    use ImageManualSortTrait;

    public const TYPE = 'poi';
    public const FULL_SIZE = 1200;
    public const THUMB_SIZE = 600;
    public const TMP_MEDIA_FOLDER = 'tmp-img';

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
        'ytb',
        'show',
        'author',
        'views',
        'views_month',
        'views_today',
        'copyright',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'show'=> 'boolean',
        'cant_geocode'=> 'boolean',
    ];

    public static function boot()
    {
        parent::boot();
        self::created(function($poi) {
                PoiGeocodeJob::dispatch($poi);
        });
        self::updated(function($poi) {
            if (($poi->isDirty('lat') || $poi->isDirty('lng')) && !$poi->cant_geocode) {
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

    public static function nearest($lat, $lng): Builder
    {
        return self::query()->select(DB::raw("*,
                111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(lat))
                     * COS(RADIANS($lat))
                     * COS(RADIANS(lng - $lng))
                     + SIN(RADIANS(lat))
                     * SIN(RADIANS($lat))))) AS 'dist'"))
            ->where('lat', '<>', 0)
            ->where('lng', '<>', 0)
            ->havingRaw('dist IS NOT NULL')
            ->orderBy('dist');
    }

    public function twits(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
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

    public function checkins(): HasMany
    {
        return $this->hasMany(Checkin::class);
    }

    public function getDefaultRelationsAttribute(): array
    {
        return ['locations', 'tags', 'user', 'routes', 'checkins.user'];
    }
}
