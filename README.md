## About
TerryApiBundle is a Symfony Bundle to create REST APIs embedding the symfony/serializer and symfony/validator and takes care of HTTP related things like e.g. Headers.

![](https://github.com/simon-schubert/terry-api/workflows/php/badge.svg)

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

Example:
```php
/**
 * @Route("/candy/save", methods={"POST"}, name="candy_save")
 */
public function candySave(CandyStruct $candy)
{
    // do something

    return $iAmAnInstanceOfCandy;
}
```

## For development setup
1. copy docker/php-fpm/.env.dist to docker/php-fpm/.env and adjust to your needs
1. pull latest image(s): docker-compose pull
1. build the image(s): docker-compose build
1. create the container(s): docker-compose up -d