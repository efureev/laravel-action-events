<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Entity;

class ActionEventType
{
    /**
     * @var string Action Type: change target data
     * @example change data into model or raw db-query
     */
    public const CHANGE = 'change';

    /**
     * @var string Action Type: actions over target data without changes
     * @example export data|read certain data
     */
    public const READ = 'read';

    /**
     * @var string Action Type: just log an event
     * @example login success|login failed|logout
     */
    public const EVENT = 'event';

    /** @var string[] Types of the actions */
    public const TYPES = [
        self::CHANGE,
        self::READ,
        self::EVENT,
    ];
}
