<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Helpers;

use Php\Support\Exceptions\ConfigException;

final class ConfigHelper
{
    /**
     * @throws ConfigException
     */
    public static function validateUserColumnType(string $type): bool
    {
        $available = [
            'string',
            'uuid',
            'integer',
        ];

        if (!in_array($type, $available)) {
            throw new ConfigException('User type should be one of the following values: ' . implode(',', $available));
        }

        return true;
    }
}
