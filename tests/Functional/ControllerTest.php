<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Functional;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Violines\RestBundle\Tests\Stub\MimeTypes;
use Violines\RestBundle\ViolinesRestBundle;

/**
 * @coversNothing
 */
final class ControllerTest extends TestCase
{
    private static TestKernel $app;

    public static function setUpBeforeClass(): void
    {
        static::$app = new TestKernel();
        static::$app->boot();
    }

    public function testReturnsOne(): void
    {
        $request = Request::create('/returnsOne');
        $request->headers->set('Accept', MimeTypes::APPLICATION_JSON);

        $response = static::$app->handle($request);

        self::assertSame(MimeTypes::APPLICATION_JSON, $response->headers->get('Content-Type'));
        self::assertJsonStringEqualsJsonString(<<<JSON
            {"to":"Forest"}
       JSON, $response->getContent());
    }

    public function testReturnsMany(): void
    {
        $request = Request::create('/returnsMany');
        $request->headers->set('Accept', MimeTypes::APPLICATION_JSON);

        $response = static::$app->handle($request);

        self::assertSame(MimeTypes::APPLICATION_JSON, $response->headers->get('Content-Type'));
        self::assertJsonStringEqualsJsonString(<<<JSON
            [
                {"to":"Jenny"},
                {"to":"Forest"}
            ]
       JSON, $response->getContent());
    }

    public function testReconstitutesOne(): void
    {
        $submitted = <<<JSON
            {"to":"Jenny"}
        JSON;

        $request = $this->createPostRequest('/reconstitutesOne', $submitted);
        $response = static::$app->handle($request);

        self::assertJsonStringEqualsJsonString($submitted, $response->getContent());
    }

    public function testReconstitutesMultiple(): void
    {
        $submitted = <<<JSON
            [
                {"to":"Jenny"},
                {"to":"Forest"}
            ]
        JSON;

        $request = $this->createPostRequest('reconstitutesMany', $submitted);

        $response = static::$app->handle($request);

        self::assertJsonStringEqualsJsonString($submitted, $response->getContent());
    }

    private function createPostRequest(string $uri, string $body): Request
    {
        $request = Request::create($uri, Request::METHOD_POST, [], [], [], [], $body);
        $request->headers->set('Content-Type', MimeTypes::APPLICATION_JSON);
        $request->headers->set('Accept', MimeTypes::APPLICATION_JSON);

        return $request;
    }
}

/** @internal  */
final class TestKernel extends Kernel
{
    use MicroKernelTrait;
    private vfsStreamDirectory $fileStreamRoot;

    public function __construct()
    {
        parent::__construct('test', false);
        $this->fileStreamRoot = vfsStream::setup();
    }

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new ViolinesRestBundle(),
        ];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $pathControllerMap = [
            '/returnsOne' => HugController::class . '::returnsOne',
            '/returnsMany' => HugController::class . '::returnsMany',
            '/reconstitutesOne' => HugController::class . '::reconstitutesOne',
            '/reconstitutesMany' => HugController::class . '::reconstitutesMany',
        ];

        foreach ($pathControllerMap as $path => $controller) {
            $routes->add($path, $controller);
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
    }

    public function getCacheDir(): string
    {
        return $this->fileStreamRoot->url() . '/cache/';
    }

    public function getLogDir(): string
    {
        return $this->fileStreamRoot->url() . '/logs';
    }
}

final class HugController
{
    public function returnsOne(): Hug
    {
        return new Hug('Forest');
    }

    public function returnsMany(): array
    {
        return [
            new Hug('Jenny'),
            new Hug('Forest'),
        ];
    }

    public function reconstitutesOne(Hug $hug): Hug
    {
        return $hug;
    }

    /**
     * @return iterable<Hug>
     */
    public function reconstitutesMany(Hug ...$hugs): iterable
    {
        return $hugs;
    }
}

/**
 * @internal
 * @Violines\RestBundle\HttpApi\HttpApi
 */
final class Hug
{
    /**
     * @psalm-readonly
     */
    public string $to;

    public function __construct(string $to)
    {
        $this->to = $to;
    }
}
