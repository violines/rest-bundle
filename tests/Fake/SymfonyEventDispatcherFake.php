<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Fake;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SymfonyEventDispatcherFake implements EventDispatcherInterface
{
    public function dispatch($event, string $eventName = null): object
    {
        return $event;
    }
}
