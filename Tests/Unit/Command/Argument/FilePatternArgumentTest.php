<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class FilePatternArgumentTest extends TestCase
{
    #[Test] public function implementsInputArgumentInterface(): void
    {
        self::assertInstanceOf(
            InputArgumentInterface::class,
            new FilePatternArgument()
        );
    }

    #[Test] public function returnsCorrectName(): void
    {
        self::assertEquals(
            'pattern',
            FilePatternArgument::name()
        );
    }

    #[Test] public function returnsCorrectMode(): void
    {
        self::assertEquals(
            InputOption::VALUE_REQUIRED,
            FilePatternArgument::mode()
        );
    }

    #[Test] public function returnsCorrectDescription(): void
    {
        self::assertEquals(
            'pattern for log file',
            FilePatternArgument::description()
        );
    }

}
