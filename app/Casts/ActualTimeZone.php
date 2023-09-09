<?php

namespace App\Casts;

use App\Helper;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ActualTimeZone implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return $value ? Helper::fromUtcToLocalTimezone($value) : null;
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
