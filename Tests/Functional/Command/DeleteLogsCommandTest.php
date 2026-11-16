<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Functional\Command;

use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Command\DeleteLogs;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Runs the real command against log files created below the test instance.
 * Ages are given in days and applied to the modification time.
 */
class DeleteLogsCommandTest extends FunctionalTestCase
{
    protected const LOG_DIRECTORY = 'fileadmin/logs';
    protected const DATE_PREFIX_PATTERN = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}_requests\.log/';

    protected array $coreExtensionsToLoad = ['install'];

    protected array $testExtensionsToLoad = ['t3extension_tools'];

    protected bool $initializeDatabase = false;

    protected string $logPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logPath = Environment::getPublicPath() . '/' . self::LOG_DIRECTORY;
        $this->removeDirectory($this->logPath);
        mkdir($this->logPath, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->logPath);
        parent::tearDown();
    }

    /**
     * @return array<string, array{array<string, int>, array<string, string>, list<string>, list<string>}>
     */
    public static function deletionDataProvider(): array
    {
        $today = date('Y-m-d');

        return [
            'files older than age are deleted by modification time' => [
                ['old.log' => 100, 'recent.log' => 10],
                [AgeArgument::NAME => '30', FilePatternArgument::NAME => '/\.log$/'],
                ['old.log'],
                ['recent.log'],
            ],
            'file exactly at the age limit is kept' => [
                ['limit.log' => 30, 'older.log' => 31],
                [AgeArgument::NAME => '30', FilePatternArgument::NAME => '/\.log$/'],
                ['older.log'],
                ['limit.log'],
            ],
            'files not matching the pattern are kept' => [
                ['old.log' => 100, 'old.txt' => 100],
                [AgeArgument::NAME => '30', FilePatternArgument::NAME => '/\.log$/'],
                ['old.log'],
                ['old.txt'],
            ],
            'date prefix in the file name determines the age' => [
                ['2020-01-01_requests.log' => 0, $today . '_requests.log' => 400],
                [AgeArgument::NAME => '365', FilePatternArgument::NAME => self::DATE_PREFIX_PATTERN],
                ['2020-01-01_requests.log'],
                [$today . '_requests.log'],
            ],
            'date prefix pattern with other delimiter' => [
                ['2020-01-01_requests.log' => 0],
                [AgeArgument::NAME => '365', FilePatternArgument::NAME => '#^[0-9]{4}-[0-9]{2}-[0-9]{2}_requests\.log#'],
                ['2020-01-01_requests.log'],
                [],
            ],
            'invalid date prefix falls back to modification time' => [
                ['2020-13-45_requests.log' => 100, '2020-13-46_requests.log' => 1],
                [AgeArgument::NAME => '30', FilePatternArgument::NAME => self::DATE_PREFIX_PATTERN],
                ['2020-13-45_requests.log'],
                ['2020-13-46_requests.log'],
            ],
        ];
    }

    /**
     * @param array<string, int> $files file name => age in days
     * @param array<string, string> $arguments
     * @param list<string> $expectedDeleted
     * @param list<string> $expectedKept
     */
    #[Test]
    #[DataProvider('deletionDataProvider')]
    public function commandDeletesFilesOlderThanAge(
        array $files,
        array $arguments,
        array $expectedDeleted,
        array $expectedKept,
    ): void {
        foreach ($files as $fileName => $ageInDays) {
            $this->createLogFile($fileName, $ageInDays);
        }
        $tester = $this->createCommandTester();

        $exitCode = $tester->execute([DirectoryArgument::NAME => self::LOG_DIRECTORY] + $arguments);

        self::assertSame(Command::SUCCESS, $exitCode, $tester->getDisplay());
        foreach ($expectedDeleted as $fileName) {
            self::assertFileDoesNotExist($this->logPath . '/' . $fileName);
        }
        foreach ($expectedKept as $fileName) {
            self::assertFileExists($this->logPath . '/' . $fileName);
        }
        self::assertStringContainsString(
            sprintf(DeleteLogs::MESSAGE_SUCCESS, count($expectedDeleted)),
            $tester->getDisplay()
        );
    }

    #[Test]
    public function directoryMatchingThePatternIsNotDeleted(): void
    {
        mkdir($this->logPath . '/archive.log');
        touch($this->logPath . '/archive.log', time() - 100 * 86400);
        $tester = $this->createCommandTester();

        $exitCode = $tester->execute([
            AgeArgument::NAME => '30',
            DirectoryArgument::NAME => self::LOG_DIRECTORY,
            FilePatternArgument::NAME => '/\.log$/',
        ]);

        self::assertSame(Command::SUCCESS, $exitCode, $tester->getDisplay());
        self::assertDirectoryExists($this->logPath . '/archive.log');
    }

    #[Test]
    public function fileThatCannotBeDeletedResultsInFailure(): void
    {
        $this->createLogFile('old.log', 100);
        chmod($this->logPath, 0555);
        $tester = $this->createCommandTester();

        try {
            $exitCode = $tester->execute([
                AgeArgument::NAME => '30',
                DirectoryArgument::NAME => self::LOG_DIRECTORY,
                FilePatternArgument::NAME => '/\.log$/',
            ]);
        } finally {
            chmod($this->logPath, 0777);
        }

        self::assertSame(Command::FAILURE, $exitCode, $tester->getDisplay());
        self::assertStringContainsString('Could not delete file', $tester->getDisplay());
        self::assertFileExists($this->logPath . '/old.log');
    }

    #[Test]
    public function defaultDirectoryIsVarLog(): void
    {
        $varLogPath = Environment::getVarPath() . '/log';
        if (!is_dir($varLogPath)) {
            mkdir($varLogPath, 0777, true);
        }
        $fileName = $varLogPath . '/delete-logs-test.log';
        file_put_contents($fileName, 'log');
        touch($fileName, time() - 100 * 86400);
        $tester = $this->createCommandTester();

        $exitCode = $tester->execute([
            AgeArgument::NAME => '30',
            FilePatternArgument::NAME => '/^delete-logs-test\.log$/',
        ]);

        self::assertSame(Command::SUCCESS, $exitCode, $tester->getDisplay());
        self::assertFileDoesNotExist($fileName);
    }

    /**
     * @return array<string, array{array<string, string>, int, string}>
     */
    public static function invalidInputDataProvider(): array
    {
        return [
            'missing pattern' => [
                [AgeArgument::NAME => '30', DirectoryArgument::NAME => self::LOG_DIRECTORY],
                Command::INVALID,
                sprintf(DeleteLogs::WARNING_MISSING_PARAMETER, FilePatternArgument::NAME),
            ],
            'invalid pattern' => [
                [AgeArgument::NAME => '30', DirectoryArgument::NAME => self::LOG_DIRECTORY, FilePatternArgument::NAME => '/[/'],
                Command::INVALID,
                'Invalid file pattern',
            ],
            'age not a number' => [
                [AgeArgument::NAME => 'abc', DirectoryArgument::NAME => self::LOG_DIRECTORY, FilePatternArgument::NAME => '/\.log$/'],
                Command::INVALID,
                'Invalid age',
            ],
            'negative age' => [
                [AgeArgument::NAME => '-1', DirectoryArgument::NAME => self::LOG_DIRECTORY, FilePatternArgument::NAME => '/\.log$/'],
                Command::INVALID,
                'Invalid age',
            ],
            'directory outside of allowed paths' => [
                [AgeArgument::NAME => '30', DirectoryArgument::NAME => '/etc', FilePatternArgument::NAME => '/\.log$/'],
                Command::FAILURE,
                'Invalid log file path',
            ],
            'directory does not exist' => [
                [AgeArgument::NAME => '30', DirectoryArgument::NAME => 'fileadmin/does-not-exist', FilePatternArgument::NAME => '/\.log$/'],
                Command::FAILURE,
                'Invalid log file path',
            ],
        ];
    }

    /**
     * @param array<string, string> $arguments
     */
    #[Test]
    #[DataProvider('invalidInputDataProvider')]
    public function commandRejectsInvalidInput(array $arguments, int $expectedExitCode, string $expectedMessage): void
    {
        $this->createLogFile('old.log', 100);
        $tester = $this->createCommandTester();

        $exitCode = $tester->execute($arguments);

        self::assertSame($expectedExitCode, $exitCode, $tester->getDisplay());
        self::assertStringContainsString($expectedMessage, $tester->getDisplay());
        self::assertFileExists($this->logPath . '/old.log');
    }

    #[Test]
    public function commandNameMatchesRegisteredName(): void
    {
        self::assertSame('t3extension-tools:delete-logs', DeleteLogs::DEFAULT_NAME);
    }

    protected function createCommandTester(): CommandTester
    {
        return new CommandTester(new DeleteLogs(DeleteLogs::DEFAULT_NAME));
    }

    protected function createLogFile(string $fileName, int $ageInDays): void
    {
        $path = $this->logPath . '/' . $fileName;
        file_put_contents($path, 'log');
        touch($path, time() - $ageInDays * 86400);
    }

    protected function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($path);
    }
}
