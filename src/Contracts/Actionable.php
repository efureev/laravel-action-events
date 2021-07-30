<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Contracts;

/**
 * Interface Actionable
 * @package Fureev\ActionEvents\Contracts
 *
 * For events
 */
interface Actionable
{
    public function getName(): string;
}
