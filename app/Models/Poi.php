<?php

namespace App\Models;

use App\Jobs\PoiGeocodeJob;
use App\Models\Traits\ImageManualSortTrait;
use App\Models\Traits\ImageSizesTrait;
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
        'name_en',
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
        'show' => 'boolean',
        'cant_geocode' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();
        self::created(function(self $poi) {
            PoiGeocodeJob::dispatch($poi);
        });
        self::updating(function(self $poi) {
            if ($poi->isDirty('name')) {
                $poi->name_en = null;
            }
        });
        self::updated(function(self $poi) {
            if (($poi->isDirty('lat') || $poi->isDirty('lng')) && !$poi->cant_geocode) {
                PoiGeocodeJob::dispatch($poi);
            }
            if ($poi->isDirty('name')) {
                $poi->name_en = null;
                $poi->save();
            }
        });
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'relationship', 'postid', 'tagid');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class)
            ->orderBy('count', 'desc');
    }

    /**
     * Расстояние в км до точки (lat, lng) как alias dist. Три подстановки: lat, lng, lat.
     * Координаты приводим к double precision: PDO шлёт строки, а RADIANS(text) в PostgreSQL не существует.
     * HAVING без GROUP BY с алиасом в PostgreSQL не работает — фильтр по NULL не нужен,
     * lat/lng = NULL отсекаются условиями <> 0.
     */
    public const DIST_SQL = '*, 111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(lat))
                * COS(RADIANS(CAST(? AS double precision)))
                * COS(RADIANS(lng - CAST(? AS double precision)))
                + SIN(RADIANS(lat))
                * SIN(RADIANS(CAST(? AS double precision)))))) AS dist';

    public function getNearestAttribute()
    {
        return Cache::tags('nearest')->remember('nearest:' . $this->id, 24 * 60 * 60, function() {
            return self::nearest($this->lat, $this->lng)
                ->where('id', '<>', $this->id)
                ->limit(3)->get();
        });
    }

    public static function nearest($lat, $lng): Builder
    {
        return self::query()
            ->selectRaw(self::DIST_SQL, [$lat, $lng, $lat])
            ->where('lat', '<>', 0)
            ->where('lng', '<>', 0)
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
