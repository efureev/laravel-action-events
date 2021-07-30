<?php

namespace Fureev\ActionEvents\Tests\Entity\Events;

use Fureev\ActionEvents\Contracts\Actionable;

class Login implements Actionable
{
    public function getName(): string
    {
        return 'login';
    }
}
