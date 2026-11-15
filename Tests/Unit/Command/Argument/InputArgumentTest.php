<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class InputArgumentTest extends TestCase
{
    /**
     * @return array<string, array{class-string<InputArgumentInterface>, string, ?int, string, ?string}>
     */
    public static function argumentDataProvider(): array
    {
        return [
            'age' => [AgeArgument::class, 'age', InputOption::VALUE_REQUIRED, 'min age for files to delete', '90'],
            'directory' => [DirectoryArgument::class, 'directory', InputOption::VALUE_REQUIRED, 'directory path for log files', 'var/log'],
            'pattern' => [FilePatternArgument::class, 'pattern', InputOption::VALUE_REQUIRED, 'pattern for log file', null],
        ];
    }

    /**
     * @param class-string<InputArgumentInterface> $className
     */
    #[Test]
    #[DataProvider('argumentDataProvider')]
    public function argumentProvidesItsProperties(
        string $className,
        string $name,
        ?int $mode,
        string $description,
        ?string $default,
    ): void {
        self::assertContains(InputArgumentInterface::class, class_implements($className));
        self::assertSame($name, $className::name());
        self::assertSame($mode, $className::mode());
        self::assertSame($description, $className::description());
        self::assertSame($default, $className::defaultValue());
    }
}
