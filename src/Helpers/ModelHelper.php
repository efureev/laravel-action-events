<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Helpers;

use Illuminate\Database\Eloquent\Model;

final class ModelHelper
{
    public static function hasSoftDelete(Model $model): bool
    {
        return method_exists($model, 'isForceDeleting') && $model->isForceDeleting();
    }
}
