<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $model->slug = $model->slug
                ?? self::getUniqueSlug($model);
        });
    }

    public static function slugFrom(): string
    {
        return 'title';
    }

    public static function slugSeparator(): string
    {
        return '-';
    }

    private static function getUniqueSlug(Model $model): string
    {
        $separator = self::slugSeparator();
        $title = $model->{self::slugFrom()};
        $slug = Str::slug($title, $separator);

        $count = $model::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}{$separator}{$count}" : $slug;
    }
}
