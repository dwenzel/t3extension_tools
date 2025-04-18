<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class AgeArgumentTest extends TestCase
{
    #[Test] public function implementsInputArgumentInterface(): void
    {
        $this->assertInstanceOf(
            InputArgumentInterface::class,
            new AgeArgument()
        );
    }

    #[Test] public function returnsCorrectName(): void
    {
        $this->assertEquals(
            'age',
            AgeArgument::name()
        );
    }

    #[Test] public function returnsCorrectMode(): void
    {
        $this->assertEquals(
            InputOption::VALUE_REQUIRED,
            AgeArgument::mode()
        );
    }

    #[Test] public function returnsCorrectDescription(): void
    {
        $this->assertEquals(
            'min age for files to delete',
            AgeArgument::description()
        );
    }

}
