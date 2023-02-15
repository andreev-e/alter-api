<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Location extends Model
{
    protected $fillable = [
        'name',
        'type',
        'url',
        'parent',
        'count',
        'lat',
        'lng',
        'scale',
        'name_rod',
        'name_en',
        'name_dat_ed',
        'name_rod_ed',
        'name_predlozh_ed',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function pois()
    {
        return $this->belongsToMany(Poi::class);
    }

    public function getParents(array $parents = []): array
    {
        if ($this->parent !== 0) {
            $parent = self::find($this->parent);
            if (is_object($parent)) {
                // $parents[] = ['id' => $parent->id, 'name' => $parent->name, 'url' => $parent->url];
                array_unshift($parents, ['id' => $parent->id, 'name' => $parent->name, 'url' => $parent->url]);
                if ($parent->parent !== 0) {
                    if (count($parents) > 5) {
                        dd();
                    }
                    $parents = $parent->getParents($parents);
                }
            }
        }
        return $parents;
    }

    public function children(): hasMany
    {
        return $this->hasMany(self::class, 'parent', 'id');
    }

    public function parent(): hasMany
    {
        return $this->hasMany(self::class, 'id', 'parent');
    }

    public function getTagsAttribute(): Collection
    {
        return Cache::remember('location-tags:' . $this->id, 24 * 60 * 60, function() {
            $collection = [];
            $this->pois()->with('tags')
                ->chunk(200, function($pois) use (&$collection) {
                    foreach ($pois as $poi) {
                        foreach ($poi->tags as $tag) {
                            $collection[$tag->id] = $tag;
                        }
                    }
                });
            return collect(array_values($collection));
        });
    }
}
