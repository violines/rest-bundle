## About
violines/rest-bundle is a Symfony Bundle to create REST APIs. It focusses on HTTP standards and integrates the symfony/serializer and symfony/validator.

[![build](https://github.com/violines/rest-bundle/workflows/build/badge.svg)](https://github.com/violines/rest-bundle)
[![Code Coverage](https://codecov.io/gh/violines/rest-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/violines/rest-bundle/)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fviolines%2Frest-bundle%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/violines/rest-bundle/master)
[![type coverage](https://shepherd.dev/github/violines/rest-bundle/coverage.svg)](https://shepherd.dev/github/violines/rest-bundle)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=violines_rest-bundle&metric=sqale_index)](https://sonarcloud.io/dashboard?id=violines_rest-bundle)
[![Software License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Wiki Docs](https://img.shields.io/badge/wiki-docs-B29700)](https://github.com/violines/rest-bundle/wiki)

### Features
* Request body or query string to object conversion
* Response building from objects
* Configurable content negotiation
* Events to control symfony/serializer context
* Integration of symfony/validator
* Error Handling
* Optional Logging

### Compatible with...
* Symfony 5.4 + 6
* PHP 8 + 8.1

### Designed for...
modern architectures that apply Domain Driven Design principles, hexagonal architecture or similar concepts.

### Install
```sh
composer require violines/rest-bundle
```

### How does it work?
1. Create any PHP class (Entity, DTO, Command, Query, etc) and add the `#[HttpApi]` attribute or `@HttpApi` annotation
1. Use any property attributes/annotations from symfony/serializer or symfony/validator
1. Declare this PHP class as type of a controller argument
1. Return an instance of this PHP class in the controller

### Show Case
Find a sample of usage under: https://github.com/violines/rest-bundle-showcase.

## Example

```php
<?php

declare(strict_types=1);

namespace App\Entity;

#[Violines\RestBundle\HttpApi\HttpApi]
final class Order
{
    public $amount;
    public $articles;
}

// Or use Doctrine Annotations (requires separate install):
// In order to use Annotations you have to install `doctrine/annotations` via `composer require doctrine/annotations`

/**
 * @Violines\RestBundle\HttpApi\HttpApi
 */
final class Order {}
```

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AuthenticationFailedException;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController
{
    /**
     * @return Order[]
     */
    #[Route('/orders', methods: ['GET'], name: 'find_orders')]
    public function findOrders(): array
    {
        return $this->orderRepository->findOrders();
    }

    #[Route('/order/{id}', methods: ['GET'], name: 'find_order')]
    public function findOrder(int $id): Order
    {
        $order = $this->orderRepository->find($id);

        if (null === $order) {
            throw NotFoundException::id($id);
        }

        return $order;
    }

    /**
     * @param Order[] $orders
     */
    #[Route('/orders/create', methods: ['POST'], name: 'create_orders')]
    public function createOrders(Order ...$orders): Response
    {
        $this->orderRepository->createOrders($orders);

        return new Response(null, Response::HTTP_CREATED);
    }

    #[Route('/order/create', methods: ['POST'], name: 'create_order')]
    public function createOrder(Order $order): Response
    {
        $this->orderRepository->create($order);

        return new Response(null, Response::HTTP_CREATED);
    }
}
```

### Wiki
For more details please check [violines/rest-bundle Wiki](https://github.com/violines/rest-bundle/wiki).

## Development setup
1. copy docker/.env.dist to docker/.env and adjust to your needs
1. cd docker/
1. pull latest image(s): docker-compose pull
1. create the container(s): docker-compose up -d
1. run tests with `docker-compose exec php80 composer run test`
