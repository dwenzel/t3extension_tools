<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class AgeArgumentTest extends TestCase
{
    /**
     * @test
     */
    public function implementsInputArgumentInterface(): void
    {
        self::assertInstanceOf(
            InputArgumentInterface::class,
            new AgeArgument()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectName(): void
    {
        self::assertEquals(
            'age',
            AgeArgument::name()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectMode(): void
    {
        self::assertEquals(
            InputOption::VALUE_REQUIRED,
            AgeArgument::mode()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectDescription(): void
    {
        self::assertEquals(
            'min age for files to delete',
            AgeArgument::description()
        );
    }

}
