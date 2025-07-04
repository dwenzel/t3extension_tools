<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class DirectoryArgumentTest extends TestCase
{
    #[Test] public function implementsInputArgumentInterface(): void
    {
        self::assertInstanceOf(
            InputArgumentInterface::class,
            new DirectoryArgument()
        );
    }

    #[Test] public function returnsCorrectName(): void
    {
        self::assertEquals(
            'directory',
            DirectoryArgument::name()
        );
    }

    #[Test] public function returnsCorrectMode(): void
    {
        self::assertEquals(
            InputOption::VALUE_REQUIRED,
            DirectoryArgument::mode()
        );
    }

    #[Test] public function returnsCorrectDescription(): void
    {
        self::assertEquals(
            'directory path for log files',
            DirectoryArgument::description()
        );
    }
}
