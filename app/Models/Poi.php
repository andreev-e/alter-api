<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class Poi extends Model
{
    protected $table = 'poi';

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

    public function getNearestAttribute(): Collection
    {
        return Cache::remember('nearest:' . $this->id, 0, static function() {
            return self::query()
                ->select(DB::raw('(6371 * acos(cos(radians(lat)) * cos(radians(lat)) * cos(radians(lng) - radians(lng)) + sin(radians(lat)) * sin(radians(lat)))) AS `dist`'))
                ->orderBy('dist', 'desc')->limit(5)->get();
        });
    }

    public function twits(): HasMany
    {
        return $this->hasMany(Comment::class, 'backlink')->where('approved', '=', 1)->orderBy('time', 'desc');
    }

}
