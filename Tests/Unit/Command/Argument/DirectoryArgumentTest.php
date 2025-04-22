<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class DirectoryArgumentTest extends TestCase
{
    /**
     * @test
     */
    public function implementsInputArgumentInterface(): void
    {
        self::assertInstanceOf(
            InputArgumentInterface::class,
            new DirectoryArgument()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectName(): void
    {
        self::assertEquals(
            'directory',
            DirectoryArgument::name()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectMode(): void
    {
        self::assertEquals(
            InputOption::VALUE_REQUIRED,
            DirectoryArgument::mode()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectDescription(): void
    {
        self::assertEquals(
            'directory path for log files',
            DirectoryArgument::description()
        );
    }
}
