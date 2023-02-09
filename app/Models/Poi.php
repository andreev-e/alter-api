<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Poi extends Model
{
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
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'show'=> 'boolean',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'relationship', 'POSTID', 'TAGID')
            ->where('TYPE', 0);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'relationship', 'POSTID', 'TAGID')
            ->where('TYPE', '<>', 0)
            ->orderBy('COUNT', 'desc');
    }

    public function getNearestAttribute()
    {
        return Cache::remember('nearest:' . $this->id, 1000 * 60 * 60, function() {
            return self::query()
                ->select(DB::raw("*, (6371 * acos(cos(radians($this->lat)) * cos(radians(lat)) * cos(radians(lng) - radians($this->lng)) +
                    sin(radians($this->lat)) * sin(radians($this->lat)))) AS 'dist'"))
                ->where('lat', '<>', 0)
                ->where('lng', '<>', 0)
                ->where('id', '<>', $this->id)
                ->havingRaw('dist IS NOT NULL')
                ->orderBy('dist')->limit(4)->get();
        });
    }

    public function twits(): HasMany
    {
        return $this->hasMany(Comment::class, 'backlink')->where('approved', '=', 1)->orderBy('time', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'username', 'author');
    }
}
