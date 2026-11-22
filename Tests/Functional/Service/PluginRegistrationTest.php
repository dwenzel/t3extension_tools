<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Functional\Service;

use DWenzel\T3extensionTools\TestPlugins\Controller\ListController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Plugins of the fixture extension "test_plugins", defined by a PHP class
 * (PhpList) and by a YAML file (YamlList), are configured and registered.
 */
class PluginRegistrationTest extends FunctionalTestCase
{
    protected const FLEX_FORM = 'FILE:EXT:test_plugins/Configuration/FlexForms/List.xml';

    protected array $coreExtensionsToLoad = ['install'];

    protected array $testExtensionsToLoad = [
        't3extension_tools',
        __DIR__ . '/../Fixtures/Extensions/test_plugins',
    ];

    protected bool $initializeDatabase = false;

    /**
     * @return array<string, array{string, string, string, string}>
     */
    public static function pluginDataProvider(): array
    {
        return [
            'PHP class' => ['PhpList', 'testplugins_phplist', 'PHP list plugin', 'Plugin defined by a PHP class'],
            'YAML file' => ['YamlList', 'testplugins_yamllist', 'YAML list plugin', 'Plugin defined in YAML'],
        ];
    }

    #[Test]
    #[DataProvider('pluginDataProvider')]
    public function pluginIsConfiguredForExtbase(string $pluginName, string $signature, string $title, string $description): void
    {
        $plugin = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['TestPlugins']['plugins'][$pluginName] ?? null;

        self::assertIsArray($plugin, 'plugin not configured');
        self::assertSame(['list', 'show'], $plugin['controllers'][ListController::class]['actions']);
        self::assertSame(['show'], $plugin['controllers'][ListController::class]['nonCacheableActions']);
    }

    #[Test]
    #[DataProvider('pluginDataProvider')]
    public function pluginIsRegisteredAsContentType(
        string $pluginName,
        string $signature,
        string $title,
        string $description,
    ): void {
        $item = $this->findContentTypeItem($signature);

        self::assertNotNull($item, 'no CType item ' . $signature);
        self::assertSame($title, $item['label']);
        self::assertSame($description, $item['description'] ?? '');
        self::assertSame('content-plugin', $item['icon']);
        self::assertSame('plugins', $item['group']);
    }

    #[Test]
    #[DataProvider('pluginDataProvider')]
    public function flexFormIsRegisteredForPluginSignature(string $pluginName, string $signature, string $title, string $description): void
    {
        self::assertSame(self::FLEX_FORM, $this->findFlexFormDataStructure($signature));
        self::assertStringContainsString(
            'pi_flexform',
            (string)($GLOBALS['TCA']['tt_content']['types'][$signature]['showitem'] ?? '')
        );
    }

    #[Test]
    public function yamlFileWithoutPluginNameIsSkipped(): void
    {
        self::assertSame(
            ['PhpList', 'YamlList'],
            array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['TestPlugins']['plugins'])
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function findContentTypeItem(string $signature): ?array
    {
        foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $item) {
            if (($item['value'] ?? null) === $signature) {
                return $item;
            }
        }

        return null;
    }

    /**
     * TYPO3 14 keeps the data structure per record type, TYPO3 13 in the
     * "ds" array of the pi_flexform column, keyed by "<list_type>,<CType>".
     */
    protected function findFlexFormDataStructure(string $signature): ?string
    {
        if ((new Typo3Version())->getMajorVersion() >= 14) {
            return $GLOBALS['TCA']['tt_content']['types'][$signature]['columnsOverrides']['pi_flexform']['config']['ds'] ?? null;
        }

        return $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $signature] ?? null;
    }
}
