<?php

declare(strict_types=1);

/*
 * This file is part of the "t3extension_tools" extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version (GPL-2.0-or-later).
 */

namespace DWenzel\T3extensionTools\Tests\Functional\Command;

use DWenzel\T3extensionTools\Command\Argument\ExampleArgument;
use DWenzel\T3extensionTools\Command\ExampleCommand;
use DWenzel\T3extensionTools\Command\Option\ExampleOption;
use DWenzel\T3extensionTools\Tests\Functional\Fixtures\Command\MissingSqlFileCommand;
use DWenzel\T3extensionTools\Tests\Functional\Fixtures\Command\SqlFileCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ExampleCommand and ExecuteSqlTrait.
 *
 * Asserts behaviour only (exit codes, database effects). The trait executes
 * the SQL through Doctrine DBAL, so no database client binary is needed.
 */
class ExampleCommandTest extends FunctionalTestCase
{
    private const EXIT_MISSING_CONNECTION = 1_641_390_076;
    private const EXIT_SQL_FAILED = 1_641_390_086;
    private const EXIT_SQL_FILE_NOT_READABLE = 1_795_339_730;
    private const TABLE = 'tx_t3extensiontools_fixture';
    private const FIXTURE_PATH = 'EXT:t3extension_tools/Tests/Functional/Fixtures/Sql/';

    protected array $coreExtensionsToLoad = ['install'];
    protected array $testExtensionsToLoad = ['t3extension_tools'];

    #[Test]
    public function commandDefinesConnectionOptionWithDefaultConnection(): void
    {
        $command = new ExampleCommand();

        self::assertTrue($command->getDefinition()->hasOption(ExampleCommand::OPTION_CONNECTION));
        self::assertSame(
            ExampleCommand::OPTION_CONNECTION_DEFAULT,
            $command->getDefinition()->getOption(ExampleCommand::OPTION_CONNECTION)->getDefault()
        );
    }

    #[Test]
    public function commandDefinesExampleArgumentAndOption(): void
    {
        $definition = (new ExampleCommand())->getDefinition();

        self::assertTrue($definition->hasArgument(ExampleArgument::NAME));
        self::assertFalse($definition->getArgument(ExampleArgument::NAME)->isRequired());
        self::assertTrue($definition->hasOption(ExampleOption::NAME));
    }

    #[Test]
    public function unknownConnectionReturnsMissingConnectionExitCode(): void
    {
        $output = $this->createStreamOutput();

        $exitCode = (new ExampleCommand())->run(
            new ArrayInput(['--' . ExampleCommand::OPTION_CONNECTION => 'DoesNotExist']),
            $output
        );

        self::assertSame(self::EXIT_MISSING_CONNECTION, $exitCode);
        self::assertStringContainsString(ExampleCommand::ERROR_MISSING_CONNECTION, $this->readOutput($output));
    }

    #[Test]
    public function exampleSqlFileIsExecutedSuccessfully(): void
    {
        $exitCode = (new ExampleCommand())->run(new ArrayInput([]), $this->createConsoleOutput());

        self::assertSame(0, $exitCode);
    }

    #[Test]
    public function commandWithMissingSqlFileIsConstructedWithoutErrorAndReportsTheFile(): void
    {
        set_error_handler(static function (int $level, string $message): never {
            throw new \ErrorException($message, 0, $level);
        });
        try {
            $command = new MissingSqlFileCommand();
        } finally {
            restore_error_handler();
        }
        $output = $this->createStreamOutput();

        $exitCode = $command->run(new ArrayInput([]), $output);

        self::assertSame(self::EXIT_SQL_FILE_NOT_READABLE, $exitCode);
        self::assertStringContainsString('does-not-exist.sql', $this->readOutput($output));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function unreadableSqlFileDataProvider(): array
    {
        return [
            'missing file' => [self::FIXTURE_PATH . 'does-not-exist.sql'],
            'directory' => [rtrim(self::FIXTURE_PATH, '/')],
            'unknown extension' => ['EXT:does_not_exist/example.sql'],
            'empty path' => [''],
        ];
    }

    #[Test]
    #[DataProvider('unreadableSqlFileDataProvider')]
    public function unreadableSqlFileReturnsFileErrorExitCode(string $path): void
    {
        $output = $this->createStreamOutput();

        $exitCode = (new SqlFileCommand($path))->run(new ArrayInput([]), $output);

        self::assertSame(self::EXIT_SQL_FILE_NOT_READABLE, $exitCode);
        self::assertStringContainsString('SQL file', $this->readOutput($output));
    }

    #[Test]
    public function unknownConnectionIsReportedBeforeSqlFileIsRead(): void
    {
        $exitCode = (new SqlFileCommand(self::FIXTURE_PATH . 'does-not-exist.sql'))->run(
            new ArrayInput(['--' . ExampleCommand::OPTION_CONNECTION => 'DoesNotExist']),
            $this->createConsoleOutput()
        );

        self::assertSame(self::EXIT_MISSING_CONNECTION, $exitCode);
    }

    #[Test]
    public function fixtureWithCommentsStringsAndSeveralStatementsPerLineIsExecuted(): void
    {
        $exitCode = (new SqlFileCommand(self::FIXTURE_PATH . 'statements.sql'))
            ->run(new ArrayInput([]), $this->createConsoleOutput());

        self::assertSame(0, $exitCode);
        self::assertSame(
            [
                1 => "semicolon at end of line;\ninside a string",
                2 => 'it\'s -- no comment',
                3 => '/* no comment */',
            ],
            $this->selectTitles()
        );
    }

    #[Test]
    public function fixtureWithCommentsOnlyIsExecutedSuccessfully(): void
    {
        $exitCode = (new SqlFileCommand(self::FIXTURE_PATH . 'comments-only.sql'))
            ->run(new ArrayInput([]), $this->createConsoleOutput());

        self::assertSame(0, $exitCode);
    }

    #[Test]
    public function executionStopsAtFirstFailingStatement(): void
    {
        $output = $this->createStreamOutput();

        $exitCode = (new SqlFileCommand(self::FIXTURE_PATH . 'failing.sql'))->run(new ArrayInput([]), $output);

        self::assertSame(self::EXIT_SQL_FAILED, $exitCode);
        self::assertStringContainsString(ExampleCommand::ERROR_SQL_EXECUTION_FAILED, $this->readOutput($output));
        self::assertSame([1 => 'first'], $this->selectTitles());
    }

    #[Test]
    public function exampleSqlFileIsExecutedWithNonConsoleOutput(): void
    {
        $exitCode = (new ExampleCommand())->run(
            new ArrayInput([]),
            $this->createStreamOutput()
        );

        self::assertSame(0, $exitCode);
    }

    private function createConsoleOutput(): ConsoleOutput
    {
        return new ConsoleOutput(OutputInterface::VERBOSITY_QUIET);
    }

    private function createStreamOutput(): StreamOutput
    {
        $stream = fopen('php://memory', 'w+');
        self::assertIsResource($stream);

        return new StreamOutput($stream);
    }

    /**
     * @return array<int, string>
     */
    private function selectTitles(): array
    {
        $titles = [];
        $rows = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName('Default')
            ->executeQuery('SELECT uid, title FROM ' . self::TABLE . ' ORDER BY uid')
            ->fetchAllAssociative();
        foreach ($rows as $row) {
            $titles[(int)$row['uid']] = (string)$row['title'];
        }

        return $titles;
    }

    private function readOutput(StreamOutput $output): string
    {
        rewind($output->getStream());

        return (string)stream_get_contents($output->getStream());
    }
}
