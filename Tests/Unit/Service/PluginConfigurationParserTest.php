<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Service;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use DWenzel\T3extensionTools\Service\PluginConfigurationParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class PluginConfigurationParserTest extends TestCase
{
    protected const FIXTURE_FILE = __DIR__ . '/../Fixtures/Packages/with_plugins/Configuration/Plugins/First.yaml';

    protected PluginConfigurationParser $subject;

    protected function setUp(): void
    {
        $this->subject = new PluginConfigurationParser();
    }

    /**
     * @return array<string, array{string, array<string, mixed>}>
     */
    public static function pluginDataProvider(): array
    {
        return [
            'complete plugin section' => [
                <<<YAML
                plugin:
                  extensionName: 'TestExtension'
                  name: 'TestPlugin'
                  type: 'CType'
                  controllerActions:
                    Test: 'list,show'
                  nonCacheableControllerActions:
                    Test: 'list'
                YAML,
                [
                    'getExtensionName' => 'TestExtension',
                    'getPluginName' => 'TestPlugin',
                    'getPluginType' => ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
                    'getControllerActions' => ['Test' => 'list,show'],
                    'getNonCacheableControllerActions' => ['Test' => 'list'],
                ],
            ],
            'type defaults to content element, actions to empty arrays' => [
                <<<YAML
                plugin:
                  extensionName: 'TestExtension'
                  name: 'TestPlugin'
                YAML,
                [
                    'getPluginType' => ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
                    'getControllerActions' => [],
                    'getNonCacheableControllerActions' => [],
                ],
            ],
            'type list_type is kept' => [
                <<<YAML
                plugin:
                  extensionName: 'TestExtension'
                  name: 'TestPlugin'
                  type: 'list_type'
                YAML,
                ['getPluginType' => 'list_type'],
            ],
            'actions as list' => [
                <<<YAML
                plugin:
                  extensionName: 'TestExtension'
                  name: 'TestPlugin'
                  controllerActions:
                    Test: ['list', 'show']
                YAML,
                ['getControllerActions' => ['Test' => ['list', 'show']]],
            ],
            'complete registration section' => [
                <<<YAML
                plugin:
                  extensionName: 'TestExtension'
                  name: 'TestPlugin'
                registration:
                  title: 'Test Plugin'
                  description: 'Test Description'
                  icon: 'test-icon'
                  group: 'test'
                  flexForm: 'FILE:EXT:test/flexform.xml'
                YAML,
                [
                    'getPluginTitle' => 'Test Plugin',
                    'getPluginDescription' => 'Test Description',
                    'getPluginIcon' => 'test-icon',
                    'getPluginGroup' => 'test',
                    'getFlexForm' => 'FILE:EXT:test/flexform.xml',
                ],
            ],
            'registration values default to empty strings' => [
                <<<YAML
                plugin:
                  extensionName: 'TestExtension'
                  name: 'TestPlugin'
                YAML,
                [
                    'getPluginTitle' => '',
                    'getPluginDescription' => '',
                    'getPluginIcon' => '',
                    'getPluginGroup' => '',
                    'getFlexForm' => '',
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expectedValues getter => expected value
     */
    #[Test]
    #[DataProvider('pluginDataProvider')]
    public function parseYamlReturnsConfigurationWithExpectedValues(string $yaml, array $expectedValues): void
    {
        $result = $this->subject->parseYaml($yaml);

        self::assertInstanceOf(PluginConfigurationInterface::class, $result);
        self::assertInstanceOf(PluginRegistrationInterface::class, $result);
        foreach ($expectedValues as $getter => $expected) {
            self::assertSame($expected, $result->$getter(), $getter);
        }
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidYamlDataProvider(): array
    {
        return [
            'empty content' => [''],
            'missing plugin section' => ["registration:\n  title: 'Test Plugin'"],
            'invalid syntax' => ["plugin:\n  name: 'a\n - b"],
            'plugin section is not a map' => ["plugin: 'TestPlugin'"],
            'missing extension name' => ["plugin:\n  name: 'TestPlugin'"],
            'empty extension name' => ["plugin:\n  extensionName: ''\n  name: 'TestPlugin'"],
            'missing plugin name' => ["plugin:\n  extensionName: 'TestExtension'"],
            'plugin name is not a string' => ["plugin:\n  extensionName: 'TestExtension'\n  name: ['a']"],
            'controller actions are not a map' => ["plugin:\n  extensionName: 'TestExtension'\n  name: 'TestPlugin'\n  controllerActions: 'list'"],
            'registration section is not a map' => ["plugin:\n  extensionName: 'TestExtension'\n  name: 'TestPlugin'\nregistration: 'x'"],
        ];
    }

    #[Test]
    #[DataProvider('invalidYamlDataProvider')]
    public function parseYamlThrowsExceptionForInvalidContent(string $yaml): void
    {
        $this->expectException(\RuntimeException::class);

        $this->subject->parseYaml($yaml);
    }

    #[Test]
    public function parseFileReturnsConfigurationFromFile(): void
    {
        $result = $this->subject->parseFile(self::FIXTURE_FILE);

        self::assertInstanceOf(PluginConfigurationInterface::class, $result);
        self::assertSame('First', $result->getPluginName());
    }

    #[Test]
    public function parseFileThrowsExceptionForMissingFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(8656676970);

        $this->subject->parseFile(__DIR__ . '/does-not-exist.yaml');
    }
}
