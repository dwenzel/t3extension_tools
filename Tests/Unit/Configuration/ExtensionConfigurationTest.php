<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Configuration;

use DWenzel\T3extensionTools\Configuration\ExtensionConfiguration;
use DWenzel\T3extensionTools\Configuration\InvalidConfigurationException;
use DWenzel\T3extensionTools\Configuration\ModuleRegistrationInterface;
use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class InvalidIconProviderClass
{

}
class ExtensionConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function registerUpdateWizardsRegistersWizardsInGlobals(): void
    {
        // Save initial state
        $initialGlobals = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'] ?? [];

        // Create a test class extending ExtensionConfiguration
        $testClass = new class extends ExtensionConfiguration {
            public const UPDATE_WIZARDS_TO_REGISTER = [
                'testWizard' => 'TestWizardClass',
                'testWizard2' => 'TestWizardClass2',
            ];
        };

        $testClass::registerUpdateWizards();

        // Assert that wizards are registered
        $this->assertArrayHasKey(
            'testWizard',
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']
        );
        $this->assertEquals(
            'TestWizardClass',
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['testWizard']
        );

        $this->assertArrayHasKey(
            'testWizard2',
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']
        );
        $this->assertEquals(
            'TestWizardClass2',
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['testWizard2']
        );

        // Restore initial state
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'] = $initialGlobals;
    }

    /**
     * @test
     */
    public function registerIconsWithInvalidProviderThrowsException(): void
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

    /**
     * @test
     */
    public function registerIconsWithEmptyArrayDoesNotRegisterIcons(): void
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

}
