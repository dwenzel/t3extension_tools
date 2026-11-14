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

use DWenzel\T3extensionTools\Command\ExampleCommand;
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
    private const TABLE = 'tx_t3extensiontools_baseline';

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
    public function unknownConnectionReturnsMissingConnectionExitCode(): void
    {
        $output = new StreamOutput(fopen('php://memory', 'w+'));

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
    public function sqlStatementIsExecutedOnDefaultConnection(): void
    {
        $command = $this->createCommandWithSql(
            'DROP TABLE IF EXISTS ' . self::TABLE . ';' . "\n"
            . 'CREATE TABLE ' . self::TABLE . ' (uid INT PRIMARY KEY);' . "\n"
            . 'INSERT INTO ' . self::TABLE . ' VALUES (42);'
        );

        $exitCode = $command->run(new ArrayInput([]), $this->createConsoleOutput());

        self::assertSame(0, $exitCode);
        self::assertEquals([42], $this->selectUids());
    }

    #[Test]
    public function commentsAndMultiLineStatementsAreSupported(): void
    {
        $command = $this->createCommandWithSql(implode("\n", [
            '# hash comment',
            '-- dash comment',
            '/* block',
            '   comment; */',
            'DROP TABLE IF EXISTS ' . self::TABLE . ';',
            'CREATE TABLE ' . self::TABLE . ' (',
            '  uid INT PRIMARY KEY',
            ');',
            'INSERT INTO ' . self::TABLE . ' VALUES (1), (2);',
        ]));

        $exitCode = $command->run(new ArrayInput([]), $this->createConsoleOutput());

        self::assertSame(0, $exitCode);
        self::assertEquals([1, 2], $this->selectUids());
    }

    #[Test]
    public function executionStopsAtFirstFailingStatement(): void
    {
        $command = $this->createCommandWithSql(implode("\n", [
            'DROP TABLE IF EXISTS ' . self::TABLE . ';',
            'CREATE TABLE ' . self::TABLE . ' (uid INT PRIMARY KEY);',
            'INSERT INTO ' . self::TABLE . ' VALUES (1);',
            'THIS IS NOT VALID SQL;',
            'INSERT INTO ' . self::TABLE . ' VALUES (2);',
        ]));
        $output = new StreamOutput(fopen('php://memory', 'w+'));

        $exitCode = $command->run(new ArrayInput([]), $output);

        self::assertSame(self::EXIT_SQL_FAILED, $exitCode);
        self::assertStringContainsString(ExampleCommand::ERROR_SQL_EXECUTION_FAILED, $this->readOutput($output));
        self::assertEquals([1], $this->selectUids());
    }

    #[Test]
    public function failingSqlStatementReturnsExecutionFailedExitCode(): void
    {
        $command = $this->createCommandWithSql('THIS IS NOT VALID SQL;');

        $exitCode = $command->run(new ArrayInput([]), $this->createConsoleOutput());

        self::assertSame(self::EXIT_SQL_FAILED, $exitCode);
    }

    #[Test]
    public function exampleSqlFileIsExecutedWithNonConsoleOutput(): void
    {
        $exitCode = (new ExampleCommand())->run(
            new ArrayInput([]),
            new StreamOutput(fopen('php://memory', 'w+'))
        );

        self::assertSame(0, $exitCode);
    }

    private function createConsoleOutput(): ConsoleOutput
    {
        return new ConsoleOutput(OutputInterface::VERBOSITY_QUIET);
    }

    private function createCommandWithSql(string $sql): ExampleCommand
    {
        $command = new ExampleCommand();
        $property = new \ReflectionProperty($command, 'sqlToExecute');
        $property->setValue($command, $sql);

        return $command;
    }

    /**
     * @return list<int>
     */
    private function selectUids(): array
    {
        return array_map(
            'intval',
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionByName('Default')
                ->executeQuery('SELECT uid FROM ' . self::TABLE . ' ORDER BY uid')
                ->fetchFirstColumn()
        );
    }

    private function readOutput(StreamOutput $output): string
    {
        rewind($output->getStream());

        return (string)stream_get_contents($output->getStream());
    }
}
