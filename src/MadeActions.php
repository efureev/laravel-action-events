<?php

declare(strict_types=1);

namespace Fureev\ActionEvents;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasActions
 * @package Fureev\ActionEvents
 *
 * @property Collection $madeActions
 *
 * @mixin Model
 */
trait MadeActions
{
    /**
     * Get all of the action events for the user.
     *
     * @return HasMany
     */
    public function madeActions(): HasMany
    {
        return $this->hasMany(ActionLogger::model(), 'user_id');
    }
}
