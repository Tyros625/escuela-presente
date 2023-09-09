<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    public function hasVerifiedEmail()
    {
        return $this->whereNotNull('email_verified_at');
    }
}
