<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Command;

use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Command\DeleteLogs;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DeleteLogsDefinitionTest extends TestCase
{
    #[Test]
    public function argumentsAreDefinedInOrder(): void
    {
        $definition = (new DeleteLogs())->getDefinition();

        self::assertSame(
            [AgeArgument::NAME, DirectoryArgument::NAME, FilePatternArgument::NAME],
            array_keys($definition->getArguments())
        );
    }

    /**
     * @return array<string, array{string, int|string|null}>
     */
    public static function argumentDataProvider(): array
    {
        return [
            'age' => [AgeArgument::NAME, '90'],
            'directory' => [DirectoryArgument::NAME, 'var/log'],
            'pattern' => [FilePatternArgument::NAME, null],
        ];
    }

    #[Test]
    #[DataProvider('argumentDataProvider')]
    public function argumentIsOptionalWithDefault(string $name, int|string|null $default): void
    {
        $argument = (new DeleteLogs())->getDefinition()->getArgument($name);

        self::assertFalse($argument->isRequired());
        self::assertFalse($argument->isArray());
        self::assertSame($default, $argument->getDefault());
    }

    #[Test]
    public function descriptionAndHelpAreSet(): void
    {
        $command = new DeleteLogs();

        self::assertSame(DeleteLogs::MESSAGE_DESCRIPTION_COMMAND, $command->getDescription());
        self::assertSame(DeleteLogs::MESSAGE_HELP_COMMAND, $command->getHelp());
    }
}
