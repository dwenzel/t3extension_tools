<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Configuration;

use DWenzel\T3extensionTools\Configuration\ExtensionConfiguration;
use DWenzel\T3extensionTools\Configuration\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;

class InvalidIconProviderClass {}

class ExtensionConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function registerIconsWithInvalidProviderThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        // Create a test class extending ExtensionConfiguration
        $testClass = new class () extends ExtensionConfiguration {
            public static function testRegisterWithInvalidProvider(): void
            {
                self::registerIconsWithProvider(['test' => 'path/to/icon.png'], InvalidIconProviderClass::class);
            }
        };

        $testClass::testRegisterWithInvalidProvider();
    }

    /**
     * @test
     */
    public function registerIconsWithEmptyArrayDoesNotRegisterIcons(): void
    {
        // Create a mock for IconRegistry
        $iconRegistryMock = $this->createMock(IconRegistry::class);
        $iconRegistryMock->expects(self::never())
            ->method('registerIcon');

        // Create a test class extending ExtensionConfiguration
        $testClass = new class () extends ExtensionConfiguration {
            public static function testRegisterWithEmptyArray(): void
            {
                self::registerIconsWithProvider([], SvgIconProvider::class);
            }
        };

        $testClass::testRegisterWithEmptyArray();
    }

}
