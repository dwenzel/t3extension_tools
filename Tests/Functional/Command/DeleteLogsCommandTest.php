<?php

namespace DWenzel\T3extensionTools\Tests\Functional\Command;

use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Command\DeleteLogs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteLogsCommandTest extends TestCase
{
    protected DeleteLogs $subject;
    protected string $testDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new class () extends DeleteLogs {
            public function executeTest(ArrayInput $input, BufferedOutput $output): int
            {
                return $this->execute($input, $output);
            }

            protected function initialize(InputInterface $input, OutputInterface $output): void
            {
                $this->io = new SymfonyStyle(
                    new ArrayInput([]),
                    new BufferedOutput()
                );
            }
        };

        // Create a temporary test directory for log files
        $this->testDir = sys_get_temp_dir() . '/t3extension_tools_test_' . uniqid('', true);
        mkdir($this->testDir, 0777, true);

        // Initialize the command
        //$this->subject->initialize();
    }

    protected function tearDown(): void
    {
        // Clean up any created test files
        if (is_dir($this->testDir)) {
            $files = new \DirectoryIterator($this->testDir);
            foreach ($files as $file) {
                if (!$file->isDot() && $file->isFile()) {
                    unlink($file->getPathname());
                }
            }
            rmdir($this->testDir);
        }

        parent::tearDown();
    }

    private function createTestLogFile(string $filename, int $daysOld = 0): string
    {
        $filePath = $this->testDir . '/' . $filename;
        file_put_contents($filePath, 'Test log content');

        if ($daysOld > 0) {
            // Set the modification time to X days ago
            touch($filePath, time() - ($daysOld * 86400));
        }

        return $filePath;
    }

    public function testExecuteDeletesOldFiles(): void
    {
        // Create test files with different ages
        $oldFile = $this->createTestLogFile('old_log.log', 100);
        $recentFile = $this->createTestLogFile('recent_log.log', 10);

        $input = new ArrayInput([
            AgeArgument::NAME => '30',
            DirectoryArgument::NAME => $this->testDir,
            FilePatternArgument::NAME => '/.*\.log/',
        ]);
        $output = new BufferedOutput();

        // Execute the command
        $result = $this->subject->executeTest($input, $output);

        // Check that the command executed successfully
        self::assertEquals(0, $result);

        // Verify old file was deleted and recent file still exists
        self::assertFileDoesNotExist($oldFile);
        self::assertFileExists($recentFile);

        // Check that the output contains the success message
        self::assertStringContainsString('1 file(s) successfully deleted', $output->fetch());
    }

    public function testExecuteHandlesDatePrefixedFiles(): void
    {
        // Create date-prefixed files
        $oldDatePrefixedFile = $this->createTestLogFile('2020-01-01_logs.log');
        $recentDatePrefixedFile = $this->createTestLogFile(date('Y-m-d') . '_logs.log');

        $input = new ArrayInput([
            AgeArgument::NAME => '365', // 1 year
            DirectoryArgument::NAME => $this->testDir,
            FilePatternArgument::NAME => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}_logs\.log/',
        ]);
        $output = new BufferedOutput();

        // Execute the command
        $result = $this->subject->executeTest($input, $output);

        // Check that the command executed successfully
        self::assertEquals(0, $result);

        // Verify the old date-prefixed file was deleted
        self::assertFileDoesNotExist($oldDatePrefixedFile);
        self::assertFileExists($recentDatePrefixedFile);
    }

    public function testExecuteHandlesInvalidDirectory(): void
    {
        $input = new ArrayInput([
            AgeArgument::NAME => '30',
            DirectoryArgument::NAME => '/non/existent/directory',
            FilePatternArgument::NAME => '/.*\.log/',
        ]);
        $output = new BufferedOutput();

        // Execute the command - should fail with directory error
        $result = $this->subject->executeTest($input, $output);

        // Check that the command failed
        self::assertEquals(1, $result);
        self::assertStringContainsString('Invalid log file path', $output->fetch());
    }
}
