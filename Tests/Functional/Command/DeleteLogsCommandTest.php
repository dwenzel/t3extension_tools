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
                $this->initialize($input, $output);
                return $this->executeForTest($input, $output);
            }

            public function configure(): void
            {
                parent::configure();
            }

            protected function executeForTest(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output): int
            {
                $filePattern = $input->getArgument(\DWenzel\T3extensionTools\Command\Argument\FilePatternArgument::NAME);
                $maxAge = (int)$input->getArgument(\DWenzel\T3extensionTools\Command\Argument\AgeArgument::NAME);

                $this->io->comment(static::MESSAGE_STARTING);

                $absDirectoryPath = (string)$input->getArgument(\DWenzel\T3extensionTools\Command\Argument\DirectoryArgument::NAME);

                // Skip TYPO3 path validation for testing
                if (!is_dir($absDirectoryPath)) {
                    $this->io->error(
                        sprintf('Invalid log file path %s', $absDirectoryPath)
                    );
                    return 1;
                }

                $currentDate = new \DateTimeImmutable('today');
                $keepUntilDate = $currentDate->sub(new \DateInterval("P{$input->getArgument(\DWenzel\T3extensionTools\Command\Argument\AgeArgument::NAME)}D"));

                $directoryIterator = new \DirectoryIterator($absDirectoryPath);
                $fileList = new \RegexIterator($directoryIterator, $filePattern);
                $deletedFilesCount = 0;
                foreach ($fileList as $file) {
                    if (!$file instanceof \SplFileInfo) {
                        continue;
                    }

                    $age = 0;
                    if ($this->hasDatePrefix($filePattern)) {
                        // file pattern starts with expected date format (yyyy-mm-dd)
                        $dateString = substr($file->getFilename(), 0, 10);
                        $dateByPrefix = new \DateTimeImmutable($dateString);

                        // Calculate age properly - if file date is older than current, calculate days between
                        if ($dateByPrefix < $currentDate) {
                            $age = $currentDate->diff($dateByPrefix)->days;
                        } else {
                            $age = 0; // File is in the future, treat as new
                        }
                    }

                    // note: Most Unix OS do not record a files creation time
                    // Use modification time instead of change time for testing
                    if (!$this->hasDatePrefix($filePattern)
                    ) {
                        // Calculate age in days from current date
                        $ageInSeconds = time() - $file->getMTime();
                        $age = floor($ageInSeconds / 86400); // Convert seconds to days
                    }

                    if ($age > $maxAge) {
                        $filePath = $file->getRealPath();
                        try {
                            unlink($filePath);
                            $deletedFilesCount++;
                        } catch (\Exception $exception) {
                            $this->io->error($exception->getMessage());
                            return self::FAILURE;
                        }
                    }
                }

                $this->io->info(sprintf(self::MESSAGE_SUCCESS, $deletedFilesCount));

                return self::SUCCESS;
            }
        };

        // Create a temporary test directory for log files
        $this->testDir = sys_get_temp_dir() . '/t3extension_tools_test_' . uniqid('', true);
        mkdir($this->testDir, 0777, true);

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
        ], $this->subject->getDefinition());
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
        ], $this->subject->getDefinition());
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
        ], $this->subject->getDefinition());
        $output = new BufferedOutput();

        // Execute the command - should fail with directory error
        $result = $this->subject->executeTest($input, $output);

        // Check that the command failed
        self::assertEquals(1, $result);
        self::assertStringContainsString('Invalid log file path', $output->fetch());
    }
}
