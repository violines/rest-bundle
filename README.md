## About
TerryApiBundle is a Symfony Bundle to create REST APIs. While you can focus on your data model, business logic and persistance layer implementations, TerryApiBundle handles serialization, validation and HTTP things like headers or status codes.

[![build](https://github.com/simon-schubert/terry-api/workflows/build/badge.svg)](https://github.com/simon-schubert/terry-api)
[![Code Coverage](https://codecov.io/gh/simon-schubert/terry-api/branch/master/graph/badge.svg)](https://codecov.io/gh/simon-schubert/terry-api)
[![Software License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Wiki Docs](https://img.shields.io/badge/wiki-docs-B29700)](https://github.com/simon-schubert/terry-api/wiki)

### Who should use TerryApi?
Symfony Developers who want to have full controll over what happens inside the controller: after the Arguments of the Controller are resolved and before the Controller returns.

### Install
```sh
composer require simon-schubert/terry-api
```

### How does it work?
1. Create a PHP class and add the `@TerryApiBundle\Annotation\Struct` annotation
1. Use any property annotations from symfony/serializer or symfony/validator inside your struct
1. Declare a struct as type of a controller argument 
1. Return an instance of a struct in the controller

### Show Case
You can find a sample of usage under: https://github.com/simon-schubert/terry-api-show.

### Example of a Controller in your project

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AuthenticationFailedException;
use App\Struct\Candy;
use App\Struct\Ok;
use App\Struct\User;
use Symfony\Component\Routing\Annotation\Route;

class CandyController
{
    /**
     * @Route("/candies", methods={"GET"}, name="candy_list")
     */
    public function candyList(): array
    {
        $_candies = [];

        foreach ($this->candyRepository->findAll() as $entity) {
            $_candies[] = $entity->toStruct();
        }

        return $_candies;
    }

    /**
     * @Route("/candy/{id}", methods={"GET"}, name="candy_detail")
     */
    public function candyDetail(int $id): Candy
    {
        $entity = $this->candyRepository->findOneBy(['id' => $id]);

        if (null === $candy) {
            throw NotFoundException::create();
        }

        return $entity->toStruct();
    }


    /**
     * @Route("/candy", methods={"POST"}, name="candy_save")
     */
    public function candySave(Candy $candy): Ok
    {
        // do business logic with Candy

        return Ok::create();
    }

    /**
     * @Route("/candies", methods={"POST"}, name="candies_save")
     */
    public function candiesSave(Candy ...$candies): Ok
    {
        // do business logic with Candy[]

        return Ok::create();
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