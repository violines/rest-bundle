<?php

declare(strict_types=1);

namespace TerryApiBundle\Tests\Mock;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Dispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event, ?string $eventName = null): object
    {
        return $event;
    }
}
