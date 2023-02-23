<?php

namespace App\Enums;

use App\Models\Poi;
use App\Models\Route;

enum Commentables: string
{
    case poi = Poi::class;
    case route = Route::class;

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $commentable) {
            if ($name === $commentable->name) {
                return $commentable;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class);
    }

    public static function list(): string
    {
        return implode(',', array_map(fn($commentable) => $commentable->name, self::cases()));
    }
}

