<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Contracts;

interface ModelActionEventable extends Actionable
{
    public function getChangedData(): ?array;

    public function getOriginalData(): ?array;
}
