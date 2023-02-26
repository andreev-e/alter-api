<?php

namespace App\Models;

use App\Models\Traits\ImageManualSortTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use Notifiable;
    use HasApiTokens;
    use InteractsWithMedia;
    use ImageManualSortTrait;

    public const THUMB_SIZE = 600;
    public const TMP_MEDIA_FOLDER = 'tmp-img';
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'userlevel',
        'firstname',
        'lastname',
        'about',
        'homepage',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'userid',
        'email_verified_at',
        'updated_at',
        'password_new',
    ];

    protected $appends = [
      'to_moderate'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'favorites' => 'array',
    ];

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function pois(): HasMany
    {
        return $this->hasMany(Poi::class, 'author', 'username');
    }

    /**
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        if ($media) {
            if ($media->with > self::THUMB_SIZE || $media->height > self::THUMB_SIZE) {
                $this->addMediaConversion('thumb')
                    ->fit(Manipulations::FIT_MAX, self::THUMB_SIZE, self::THUMB_SIZE);
            }
            $this->addMediaConversion('thumb');
        }
    }

    public function getToModerateAttribute()
    {
        if ($this->username === 'andreev') {
            return Poi::query()->where(function(Builder $query) {
                return $query->orWhere('show', 0)
                    ->orWhere('lat', 0)
                    ->orWhere('lng', 0);
            })->count();
        }
        return 0;
    }
}
