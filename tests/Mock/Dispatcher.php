<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Mock;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Dispatcher implements EventDispatcherInterface
{
    public function dispatch($event, string $eventName = null): object
    {
        return $event;
    }
}
