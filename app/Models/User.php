<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable  implements HasMedia
{
    use Notifiable;
    use HasApiTokens;
    use InteractsWithMedia;

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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthPassword() {
        return $this->password;
    }

    /**
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        if ($media->with > 600 || $media->height > 600) {
            $this->addMediaConversion('thumb')
                ->fit(Manipulations::FIT_MAX, 600, 600);
        }
        $this->addMediaConversion('thumb');
    }
}
