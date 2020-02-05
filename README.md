## About
TerryApiBundle is a Symfony Bundle to create REST APIs embedding the symfony/serializer and symfony/validator and takes care of HTTP related things like e.g. Headers.

[![build](https://github.com/simon-schubert/terry-api/workflows/build/badge.svg)](https://github.com/simon-schubert/terry-api)
[![Code Coverage](https://codecov.io/gh/simon-schubert/terry-api/branch/master/graph/badge.svg)](https://codecov.io/gh/simon-schubert/terry-api)
[![Software License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

### Who should use TerryApi?
Symfony Developers who want to have full controll over what happens inside the controller: after the Arguments of the Controller are resolved and before the Controller returns.

### Install
```sh
composer require simon-schubert/terry-api
```

### How does it work?
1. Create a PHP Class and add the `@TerryApiBundle\Annotation\Struct` annotation
1. Use any property annotations from symfony/serializer or symfony/validator inside your Structs
1. Declare a Struct as type of a controller argument 
1. Return an instance of a Struct in the controller


```php
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\AuthenticationFailedException;
use App\Struct\Candy;
use App\Struct\Ok;
use App\Struct\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TerryApiController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="terry_api_index")
     */
    public function index()
    {
        $_candies = [];

        for ($i = 0; $i < 5; ++$i) {
            $candy = new Candy();
            $candy->weight = 100;
            $candy->name = 'No.' . $i . 'Candy';
            $_candies[] = $candy;
        }

        return $_candies;
    }

    /**
     * @Route("/candy/{id}", methods={"GET"}, name="candy")
     */
    public function candyDetail(int $id)
    {
        return new Candy();
    }


    /**
     * @Route("/candy", methods={"POST"}, name="terry_api_candy_save")
     */
    public function candySave(Candy $candy)
    {
        // do business logic with Candy

        return Ok::create();
    }

    /**
     * @Route("/candies", methods={"POST"}, name="terry_api_candies_save")
     */
    public function candiesSave(Candy ...$candies)
    {
        // do business logic with Candy[]

        return Ok::create();
    }


    /**
     * @Route("/admin", methods={"POST"}, name="terry_api_admin")
     */
    public function admin(User $user)
    {
        // verify user

        throw new AuthenticationFailedException();
    }
}
```

## For development setup
1. copy docker/php-fpm/.env.dist to docker/php-fpm/.env and adjust to your needs
1. pull latest image(s): docker-compose pull
1. build the image(s): docker-compose build
1. create the container(s): docker-compose up -d