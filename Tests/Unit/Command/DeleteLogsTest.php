<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Command;

use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\DeleteLogs;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;

class DeleteLogsTest extends TestCase
{
    protected const PROJECT_PATH = '/project';
    protected const PUBLIC_PATH = '/project/public';
    protected const VAR_PATH = '/project/var';

    /**
     * @var array{ApplicationContext, bool, bool, string, string, string, string, string, string}
     */
    protected array $environmentBackup;

    protected DeleteLogs $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->environmentBackup = [
            Environment::getContext(),
            Environment::isCli(),
            Environment::isComposerMode(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
            Environment::getVarPath(),
            Environment::getConfigPath(),
            Environment::getCurrentScript(),
            Environment::isWindows() ? 'WINDOWS' : 'UNIX',
        ];
        Environment::initialize(
            new ApplicationContext('Testing'),
            true,
            true,
            self::PROJECT_PATH,
            self::PUBLIC_PATH,
            self::VAR_PATH,
            self::PROJECT_PATH . '/config',
            self::PUBLIC_PATH . '/index.php',
            'UNIX'
        );

        $this->subject = new class extends DeleteLogs {
            public function callHasDatePrefix(mixed $pattern): bool
            {
                return $this->hasDatePrefix($pattern);
            }

            public function callDetermineAbsoluteDirectoryPath(InputInterface $input): string
            {
                return $this->determineAbsoluteDirectoryPath($input);
            }
        };
    }

    protected function tearDown(): void
    {
        Environment::initialize(...$this->environmentBackup);
        parent::tearDown();
    }

    /**
     * @return array<string, array{mixed, bool}>
     */
    public static function patternDataProvider(): array
    {
        return [
            'date prefix' => ['/^[0-9]{4}-[0-9]{2}-[0-9]{2}_test.log/', true],
            'date prefix without suffix' => ['/^[0-9]{4}-[0-9]{2}-[0-9]{2}_/', true],
            'date not at the start' => ['/test_[0-9]{4}-[0-9]{2}-[0-9]{2}.log/', false],
            'date prefix with other delimiter' => ['#^[0-9]{4}-[0-9]{2}-[0-9]{2}_test.log#', false],
            'empty string' => ['', false],
            'null' => [null, false],
        ];
    }

    #[Test]
    #[DataProvider('patternDataProvider')]
    public function hasDatePrefixDetectsDatePrefixedPattern(mixed $pattern, bool $expected): void
    {
        // @phpstan-ignore method.notFound
        self::assertSame($expected, $this->subject->callHasDatePrefix($pattern));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function directoryDataProvider(): array
    {
        return [
            'default' => [DirectoryArgument::DEFAULT, self::VAR_PATH . '/log'],
            'relative path' => ['fileadmin/logs', self::PUBLIC_PATH . '/fileadmin/logs'],
            'relative path with dot segments' => ['fileadmin/../logs', self::PUBLIC_PATH . '/logs'],
            'absolute path' => ['/srv/logs', '/srv/logs'],
            'absolute path with dot segments' => ['/srv/app/../logs', '/srv/logs'],
        ];
    }

    #[Test]
    #[DataProvider('directoryDataProvider')]
    public function determineAbsoluteDirectoryPathReturnsExpectedPath(string $directory, string $expected): void
    {
        $input = self::createStub(InputInterface::class);
        $input->method('getArgument')->willReturnMap([[DirectoryArgument::NAME, $directory]]);

        // @phpstan-ignore method.notFound
        self::assertSame($expected, $this->subject->callDetermineAbsoluteDirectoryPath($input));
    }
}
