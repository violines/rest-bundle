<?php

declare(strict_types=1);

namespace Violines\RestBundle\Tests\HttpApi;

use PHPUnit\Framework\TestCase;
use Violines\RestBundle\HttpApi\AnnotationReaderNotInstalledException;

/**
 * @covers \Violines\RestBundle\HttpApi\AnnotationReaderNotInstalledException
 */
class AnnotationReaderNotInstalledExceptionTest extends TestCase
{
    public function testShouldCreateException(): void
    {
        $exception = AnnotationReaderNotInstalledException::doctrine();

        self::assertInstanceOf(AnnotationReaderNotInstalledException::class, $exception);
        self::assertEquals('Could not find any class that implements Doctrine\Common\Annotations\Reader. Install e.g. with \'composer req doctrine/annotations\' or use native PHP Attributes.', $exception->getMessage());
    }
}
