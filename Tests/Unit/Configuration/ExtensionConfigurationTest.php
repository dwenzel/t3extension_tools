<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Configuration;

use DWenzel\T3extensionTools\Configuration\ExtensionConfiguration;
use DWenzel\T3extensionTools\Configuration\InvalidConfigurationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class InvalidIconProviderClass {}

class ExtensionConfigurationTest extends TestCase
{
    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[Test] public function registerIconsWithInvalidProviderThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        // Create a test class extending ExtensionConfiguration
        $testClass = new class extends ExtensionConfiguration {
            public static function testRegisterWithInvalidProvider(): void
            {
                self::registerIconsWithProvider(['test' => 'path/to/icon.png'], InvalidIconProviderClass::class);
            }
        };

        $testClass::testRegisterWithInvalidProvider();
    }

    #[Test] public function registerIconsWithEmptyArrayDoesNotRegisterIcons(): void
    {
        // Create a mock for IconRegistry
        $iconRegistryMock = $this->createMock(IconRegistry::class);
        $iconRegistryMock->expects($this->never())
            ->method('registerIcon');

        // Create a test class extending ExtensionConfiguration
        $testClass = new class extends ExtensionConfiguration {
            public static function testRegisterWithEmptyArray(): void
            {
                self::registerIconsWithProvider([], SvgIconProvider::class);
            }
        };

        $testClass::testRegisterWithEmptyArray();
    }

    #[Test] public function registerIconsRegistersEachIconWithItsProvider(): void
    {
        $registered = [];
        $iconRegistryMock = $this->createMock(IconRegistry::class);
        $iconRegistryMock->expects($this->exactly(2))
            ->method('registerIcon')
            ->willReturnCallback(
                static function (string $identifier, string $provider, array $options) use (&$registered): void {
                    $registered[] = [$identifier, $provider, $options];
                }
            );
        GeneralUtility::setSingletonInstance(IconRegistry::class, $iconRegistryMock);

        $testClass = new class extends ExtensionConfiguration {
            protected const BITMAP_ICONS_TO_REGISTER = ['bitmap-icon' => 'path/to/icon.png'];
            protected const SVG_ICONS_TO_REGISTER = ['svg-icon' => 'path/to/icon.svg'];
        };

        $testClass::registerIcons();

        self::assertSame(
            [
                ['bitmap-icon', BitmapIconProvider::class, ['source' => 'path/to/icon.png']],
                ['svg-icon', SvgIconProvider::class, ['source' => 'path/to/icon.svg']],
            ],
            $registered
        );
    }
}
