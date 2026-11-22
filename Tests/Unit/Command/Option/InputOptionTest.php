<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Command\Option;

use DWenzel\T3extensionTools\Command\Option\ConnectionOption;
use DWenzel\T3extensionTools\Command\Option\ExampleOption;
use DWenzel\T3extensionTools\Command\Option\InputOptionInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;

class InputOptionTest extends TestCase
{
    /**
     * @return array<string, array{class-string<InputOptionInterface>, string, string, ?int, string, ?string}>
     */
    public static function optionDataProvider(): array
    {
        return [
            'connection' => [ConnectionOption::class, 'connection', 'c', InputOption::VALUE_OPTIONAL, 'connection identifier', 'Default'],
            'example' => [ExampleOption::class, 'exampleOption', 'eOpt', InputOption::VALUE_REQUIRED, 'example option', null],
        ];
    }

    /**
     * @param class-string<InputOptionInterface> $className
     */
    #[Test]
    #[DataProvider('optionDataProvider')]
    public function optionProvidesItsProperties(
        string $className,
        string $name,
        string $shortCut,
        ?int $mode,
        string $description,
        ?string $default,
    ): void {
        self::assertContains(InputOptionInterface::class, class_implements($className));
        self::assertSame($name, $className::name());
        self::assertSame($shortCut, $className::shortCut());
        self::assertSame($mode, $className::mode());
        self::assertSame($description, $className::description());
        self::assertSame($default, $className::defaultValue());
    }
}
