<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        # TODO: уникальный slug с рекурсивной проверкой
        # TODO: если не уникальный - добавляем счётчик

        static::creating(function (Model $model) {
            $model->slug = $model->slug
                ?? str($model->{self::slugFrom()})
                    ->append(time())
                    ->slug();
        });
    }

    public static function slugFrom(): string
    {
        return 'title';
    }
}
