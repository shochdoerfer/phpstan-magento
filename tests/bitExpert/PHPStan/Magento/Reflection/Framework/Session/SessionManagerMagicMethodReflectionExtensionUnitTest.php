<?php

/*
 * This file is part of the phpstan-magento package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\PHPStan\Magento\Reflection\Framework\Session;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\BooleanType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;

class SessionManagerMagicMethodReflectionExtensionUnitTest extends PHPStanTestCase
{

    /**
     * @var SessionManagerMagicMethodReflectionExtension
     */
    private $extension;

    /**
     * @var ClassReflection
     */
    private $classReflection;

    protected function setUp(): void
    {
        /** @var ReflectionProvider $reflectionProvider */
        $reflectionProvider = $this->getContainer()->getService('reflectionProvider');
        $this->classReflection = $reflectionProvider->getClass(SessionManagerHelper::class);
        $this->extension = new SessionManagerMagicMethodReflectionExtension();
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForGetDataMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'getData');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(MixedType::class, $variants[0]->getReturnType());
        self::assertCount(2, $params);
        self::assertInstanceOf(StringType::class, $params[0]->getType());
        self::assertInstanceOf(BooleanType::class, $params[1]->getType());
    }

    /**
     * @test
     */
    public function returnMagicMethodReflectionForGetMethod(): void
    {
        $methodReflection = $this->extension->getMethod($this->classReflection, 'getTest');

        $variants = $methodReflection->getVariants();
        $params = $variants[0]->getParameters();

        self::assertCount(1, $variants);
        self::assertInstanceOf(MixedType::class, $variants[0]->getReturnType());
        self::assertCount(1, $params);
        self::assertInstanceOf(MixedType::class, $params[0]->getType());
    }

    /**
     * @test
     * @dataProvider isMethodSupportedDataprovider
     * @param string $method
     * @param bool $expectedResult
     */
    public function hasMethodDetectSessionManager(string $method, bool $expectedResult): void
    {
        self::assertSame($expectedResult, $this->extension->hasMethod($this->classReflection, $method));
    }

    /**
     * @return mixed[]
     */
    public function isMethodSupportedDataprovider(): array
    {
        return [
            ['getTest', true],
            ['setTest', true],
            ['unsetTest', true],
            ['hasText', true],
            ['someOtherMethod', false],
        ];
    }
}
