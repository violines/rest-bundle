## About
TerryApiBundle is a Symfony Bundle to create REST APIs. While you can focus on your data model, business logic and persistance layer implementations, TerryApiBundle handles serialization, validation and HTTP related things like headers or status codes.

[![build](https://github.com/simon-schubert/terry-api/workflows/build/badge.svg)](https://github.com/simon-schubert/terry-api)
[![Code Coverage](https://codecov.io/gh/simon-schubert/terry-api/branch/master/graph/badge.svg)](https://codecov.io/gh/simon-schubert/terry-api)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fsimon-schubert%2Fterry-api%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/simon-schubert/terry-api/master)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=simon-schubert_terry-api&metric=sqale_index)](https://sonarcloud.io/dashboard?id=simon-schubert_terry-api)
[![Software License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Wiki Docs](https://img.shields.io/badge/wiki-docs-B29700)](https://github.com/simon-schubert/terry-api/wiki)

### Who should use TerryApi?
Symfony Developers who want to have full controll over what happens inside the controller: after the Arguments of the Controller are resolved and before the Controller returns. This makes it a perfect fit if you want to apply principles like DDD or hexagonal architecture.

### Install
```sh
composer require simon-schubert/terry-api
```

### How does it work?
1. Create a DTO (normal PHP class) and add the `@TerryApiBundle\Annotation\HTTPApi` annotation
1. Use any property annotations from symfony/serializer or symfony/validator inside your DTO
1. Declare your DTO as type of a controller argument 
1. Return an instance of your DTO in the controller

### Show Case
You can find a sample of usage under: https://github.com/simon-schubert/terry-api-show.

### Example of a Controller in your project

```php
<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * @TerryApiBundle\HttpApi\HttpApi
 */
final class Order
{
    public $amount;
    public $articles;
}
```


```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AuthenticationFailedException;
use App\DTO\Order;
use App\DTO\Ok;
use App\DTO\User;
use Symfony\Component\Routing\Annotation\Route;

class OrderController
{
    /**
     * @Route("/orders", methods={"GET"}, name="orders")
     * @return Order[]
     */
    public function orders(): array
    {
        return $this->orderRepository->findOrders();
    }

    /**
     * @Route("/order/{id}", methods={"GET"}, name="order")
     */
    public function order(int $id): Order
    {
        $order = $this->orderRepository->find($id);

        if (null === $order) {
            throw NotFoundException::new();
        }

        return $order;
    }


    /**
     * @Route("/create_order", methods={"POST"}, name="create_order")
     */
    public function createOrder(Order $order): Ok
    {
        // create order

        return Ok::new();
    }

    /**
     * @Route("/create_orders", methods={"POST"}, name="create_orders")
     * @param Order[] $orders
     */
    public function createOrders(Order ...$orders): Ok
    {
         // create orders

        return Ok::new();
    }


    /**
     * @Route("/admin", methods={"POST"}, name="admin")
     */
    public function admin(User $user)
    {
        // verify user

        throw AuthenticationFailedException::create();
    }
}
```

### Wiki
For more details please check [TerryApiBundle Wiki](https://github.com/simon-schubert/terry-api/wiki).

## Development setup
1. copy docker/php-fpm/.env.dist to docker/php-fpm/.env and adjust to your needs
1. pull latest image(s): docker-compose pull
1. build the image(s): docker-compose build
1. create the container(s): docker-compose up -d