<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Service;

use DWenzel\T3extensionTools\Service\PluginConfigurationScanner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Package\PackageManager;

class PluginConfigurationScannerTest extends TestCase
{
    protected const FIXTURE_PATH = __DIR__ . '/../Fixtures/Packages/';

    /**
     * @return array<string, array{list<string>, array<string, list<string>>}>
     */
    public static function packagesDataProvider(): array
    {
        return [
            'no active packages' => [[], []],
            'package with plugin configurations' => [
                ['with_plugins'],
                ['with_plugins' => ['First.yaml', 'Nested/Third.yaml', 'Second.yaml']],
            ],
            'package without plugins directory' => [['without_plugins'], []],
            'package with empty plugins directory' => [['empty_plugins'], ['empty_plugins' => []]],
            'several packages' => [
                ['without_plugins', 'with_plugins', 'empty_plugins'],
                [
                    'with_plugins' => ['First.yaml', 'Nested/Third.yaml', 'Second.yaml'],
                    'empty_plugins' => [],
                ],
            ],
        ];
    }

    /**
     * @param list<string> $packageKeys
     * @param array<string, list<string>> $expected
     */
    #[Test]
    #[DataProvider('packagesDataProvider')]
    public function findExtensionsWithPluginConfigurationsReturnsYamlFilesPerExtension(
        array $packageKeys,
        array $expected
    ): void {
        $packages = [];
        foreach ($packageKeys as $packageKey) {
            $packages[$packageKey] = $this->createPackageStub($packageKey);
        }
        $packageManager = self::createStub(PackageManager::class);
        $packageManager->method('getActivePackages')->willReturn($packages);
        $subject = new PluginConfigurationScanner($packageManager);

        $result = $subject->findExtensionsWithPluginConfigurations();
        foreach ($result as &$files) {
            sort($files);
        }

        self::assertSame($expected, $result);
    }

    #[Test]
    public function getPluginConfigurationPathReturnsPathInPluginsDirectory(): void
    {
        $packageManager = self::createStub(PackageManager::class);
        $packageManager->method('getPackage')->willReturnMap([
            ['with_plugins', $this->createPackageStub('with_plugins')],
        ]);
        $subject = new PluginConfigurationScanner($packageManager);

        self::assertSame(
            self::FIXTURE_PATH . 'with_plugins/Configuration/Plugins/First.yaml',
            $subject->getPluginConfigurationPath('with_plugins', 'First.yaml')
        );
    }

    protected function createPackageStub(string $packageKey): PackageInterface
    {
        $package = self::createStub(PackageInterface::class);
        $package->method('getPackageKey')->willReturn($packageKey);
        $package->method('getPackagePath')->willReturn(self::FIXTURE_PATH . $packageKey . '/');

        return $package;
    }
}
