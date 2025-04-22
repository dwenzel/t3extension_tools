<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Command;

use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\DeleteLogs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\PathUtility;

class DeleteLogsTest extends TestCase
{
    protected DeleteLogs $subject;

    protected InputInterface|MockObject $inputMock;

    protected OutputInterface|MockObject $outputMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new class () extends DeleteLogs {
            // Make protected methods testable
            public function isDatePrefixedPattern(string $pattern): bool
            {
                return $this->hasDatePrefix($pattern);
            }

            public function getAbsoluteDirectoryPath(InputInterface $input): string
            {
                return $this->determineAbsoluteDirectoryPath($input);
            }
        };

        $this->inputMock = $this->createMock(InputInterface::class);
        $this->outputMock = $this->createMock(OutputInterface::class);
    }

    /**
     * @test
     */
    public function hasDatePrefixReturnsTrueForDatePrefixedPattern(): void
    {
        $pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}_test.log/';

        self::assertTrue($this->subject->isDatePrefixedPattern($pattern));
    }

    /**
     * @test
     */
    public function hasDatePrefixReturnsFalseForNonDatePrefixedPattern(): void
    {
        $pattern = '/test_[0-9]{4}-[0-9]{2}-[0-9]{2}.log/';

        self::assertFalse($this->subject->isDatePrefixedPattern($pattern));
    }

    /**
     * @test
     */
    public function determineAbsoluteDirectoryPathReturnsVarLogForDefaultArgument(): void
    {
        self::markTestSkipped('static class Environment cannot be mocked');
        $expectedPath = '/var/log';

        // Mock Environment class
        Environment::expects(self::once())
            ->method('getVarPath')
            ->willReturn('/var');

        $this->inputMock->expects(self::once())
            ->method('getArgument')
            ->with(DirectoryArgument::NAME)
            ->willReturn(DirectoryArgument::DEFAULT);

        self::assertEquals(
            $expectedPath,
            $this->subject->getAbsoluteDirectoryPath($this->inputMock)
        );
    }

    /**
     * @test
     */
    public function determineAbsoluteDirectoryPathReturnsAbsolutePathIfProvided(): void
    {
        $absolutePath = '/absolute/path/to/logs';

        $this->inputMock->expects(self::once())
            ->method('getArgument')
            ->with(DirectoryArgument::NAME)
            ->willReturn($absolutePath);

        self::assertEquals(
            $absolutePath,
            $this->subject->getAbsoluteDirectoryPath($this->inputMock)
        );
    }

    /**
     * @test
     */
    public function determineAbsoluteDirectoryPathReturnsPublicPathForRelativePath(): void
    {
        self::markTestSkipped('static classes Environment and PathUtility cannot be mocked');
        $relativePath = 'relative/path';
        $expectedPath = '/public/relative/path';

        $this->inputMock->expects(self::once())
            ->method('getArgument')
            ->with(DirectoryArgument::NAME)
            ->willReturn($relativePath);

        // Mock Environment class
        /*
        Environment::expects($this->once())
            ->method('getPublicPath')
            ->willReturn('/public');

        // Mock PathUtility to ensure relative path detection
        PathUtility::expects($this->once())
            ->method('isAbsolutePath')
            ->with($relativePath)
            ->willReturn(false);

        // Mock PathUtility for canonical path
        PathUtility::expects($this->once())
            ->method('getCanonicalPath')
            ->with($relativePath)
            ->willReturn($relativePath);
        */
        self::assertEquals(
            $expectedPath,
            $this->subject->getAbsoluteDirectoryPath($this->inputMock)
        );
    }
}
