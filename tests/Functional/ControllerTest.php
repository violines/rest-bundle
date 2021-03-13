<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Violines\RestBundle\Tests\Stubs\MimeTypes;
use Violines\RestBundle\ViolinesRestBundle;
use function sys_get_temp_dir;

/**
 * @coversNothing
 */
final class ControllerTest extends TestCase
{
    private TestKernel $app;

    protected function setUp(): void
    {
        $this->deleteTempDir();

        $this->app = new TestKernel();
        $this->app->boot();
    }

    protected function tearDown(): void
    {
        $this->deleteTempDir();
    }

    public function testReturnsOne(): void
    {
        $request = Request::create('/returnsOne');
        $request->headers->set('Accept', MimeTypes::APPLICATION_JSON);

        $response = $this->app->handle($request);

        self::assertSame(MimeTypes::APPLICATION_JSON, $response->headers->get('Content-Type'));
        self::assertJsonStringEqualsJsonString(<<<JSON
            {"to":"Forest"}
       JSON, $response->getContent());
    }

    public function testReturnsMany(): void
    {
        $request = Request::create('/returnsMany');
        $request->headers->set('Accept', MimeTypes::APPLICATION_JSON);

        $response = $this->app->handle($request);

        self::assertSame(MimeTypes::APPLICATION_JSON, $response->headers->get('Content-Type'));
        self::assertJsonStringEqualsJsonString(<<<JSON
            [
                {"to":"Jenny"},
                {"to":"Forest"}
            ]
       JSON, $response->getContent());
    }

    public function testReconstitutesOnetoRequest(): void
    {
        $submitted = <<<JSON
            {"to":"Jenny"}
        JSON;

        $request = Request::create('/reconstitutesOne', Request::METHOD_POST, [], [], [], [], $submitted);
        $request->headers->set('Content-Type', MimeTypes::APPLICATION_JSON);
        $request->headers->set('Accept', MimeTypes::APPLICATION_JSON);

        $response = $this->app->handle($request);

        self::assertJsonStringEqualsJsonString($submitted, $response->getContent());
    }

    public function testReconstitutesMultipletoRequest(): void
    {
        $submitted = <<<JSON
            [
                {"to":"Jenny"},
                {"to":"Forest"}
            ]
        JSON;

        $request = Request::create('reconstitutesMany', Request::METHOD_POST, [], [], [], [], $submitted);
        $request->headers->set('Content-Type', MimeTypes::APPLICATION_JSON);
        $request->headers->set('Accept', MimeTypes::APPLICATION_JSON);

        $response = $this->app->handle($request);

        self::assertJsonStringEqualsJsonString($submitted, $response->getContent());
    }

    private function deleteTempDir(): void
    {
        $tempDir = TestKernel::getTempDir();

        if (!file_exists($tempDir)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($tempDir);
    }
}

/** @internal  */
final class TestKernel extends Kernel
{
    use MicroKernelTrait;


    public function __construct()
    {
        parent::__construct('test', false);
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
        $routes->addRoute(new Route('/returnsOne', [
            '_controller' => HugController::class . '::returnsOne',
        ]));

        $routes->addRoute(new Route('/returnsMany', [
            '_controller' => HugController::class . '::returnsMany',
        ]));

        $routes->addRoute(new Route('/reconstitutesOne', [
            '_controller' => HugController::class . '::reconstitutesOne',
        ]));

        $routes->addRoute(new Route('reconstitutesMany', [
            '_controller' => HugController::class . '::reconstitutesMany',
        ]));
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
    }

    public function getCacheDir(): string
    {
        return self::getTempDir() . '/cache/';
    }

    public function getLogDir(): string
    {
        return self::getTempDir() . '/logs';
    }

    public static function getTempDir(): string
    {
        $parts = [
            sys_get_temp_dir(),
            ControllerTest::class,
            Kernel::VERSION,
        ];

        return implode('/', $parts);
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
