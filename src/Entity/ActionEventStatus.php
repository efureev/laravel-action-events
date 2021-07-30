<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Entity;

class ActionEventStatus
{
    public const DONE    = 'done';
    public const RUNNING = 'running';
    public const FAILED  = 'failed';

    /** @var string[] Типы действий */
    public const TYPES = [
        self::DONE,
        self::RUNNING,
        self::FAILED,
    ];
}
