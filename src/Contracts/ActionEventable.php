<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Contracts;

interface ActionEventable extends Actionable
{
    public function getUserId(): mixed;

    public function getType(): string;

    public function getStatus(): string;

    public function toArray(): array;
}
