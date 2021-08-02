<?php

declare(strict_types=1);

namespace Fureev\ActionEvents;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasActions
 * @package Fureev\ActionEvents
 *
 * @property Collection $actions
 *
 * @mixin Model
 *
 * A Trait For User Model
 */
trait HasActions
{
    public function actions(): MorphMany
    {
        return $this->morphMany(ActionLogger::model(), 'target');
    }
}
