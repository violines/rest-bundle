<?php

declare(strict_types=1);

namespace TerryApiBundle\ArgumentValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TerryApiBundle\Annotation\HTTPApiReader;
use TerryApiBundle\Exception\AnnotationNotFoundException;
use TerryApiBundle\Exception\ValidationException;
use TerryApiBundle\Facade\SerializerFacade;
use TerryApiBundle\HttpClient\HttpClient;
use TerryApiBundle\HttpClient\ServerSettings;

class ObjectsArrayResolver implements ArgumentValueResolverInterface
{
    private ServerSettings $httpServer;
    private SerializerFacade $serializerFacade;
    private HTTPApiReader $httpApiReader;
    private ValidatorInterface $validator;

    public function __construct(
        ServerSettings $httpServer,
        SerializerFacade $serializerFacade,
        HTTPApiReader $httpApiReader,
        ValidatorInterface $validator
    ) {
        $this->httpServer = $httpServer;
        $this->serializerFacade = $serializerFacade;
        $this->httpApiReader = $httpApiReader;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $className = $argument->getType();

        if (
            false === $argument->isVariadic() || null === $className
            || !class_exists($className) || !is_string($request->getContent())
        ) {
            return false;
        }

        try {
            $this->httpApiReader->read($className);
        } catch (AnnotationNotFoundException $e) {
            return false;
        }

        return true;
    }

    /**
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $className = $argument->getType();
        $content = $request->getContent();
        $client = HttpClient::fromRequest($request, $this->httpServer);

        if (
            false === $argument->isVariadic() || null === $className
            || !class_exists($className) || !is_string($content)
        ) {
            throw new \LogicException('This should have been covered by self::supports(). This is a bug, please report.');
        }

        /** @var object[] $objectsArray */
        $objectsArray = $this->serializerFacade->deserialize($content, $className . '[]', $client);

        $violations = $this->validator->validate($objectsArray);

        if (0 < count($violations)) {
            throw ValidationException::create($violations);
        }

        foreach ($objectsArray as $item) {
            yield $item;
        }
    }
}
