<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class FilePatternArgumentTest extends TestCase
{
    /**
     * @test
     */
    public function implementsInputArgumentInterface(): void
    {
        $this->assertInstanceOf(
            InputArgumentInterface::class,
            new FilePatternArgument()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectName(): void
    {
        $this->assertEquals(
            'pattern',
            FilePatternArgument::name()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectMode(): void
    {
        $this->assertEquals(
            InputOption::VALUE_REQUIRED,
            FilePatternArgument::mode()
        );
    }

    /**
     * @test
     */
    public function returnsCorrectDescription(): void
    {
        $this->assertEquals(
            'pattern for log file',
            FilePatternArgument::description()
        );
    }

}
