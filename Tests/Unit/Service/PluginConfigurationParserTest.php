<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Service;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use DWenzel\T3extensionTools\Service\PluginConfigurationParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class PluginConfigurationParserTest extends TestCase
{
    /**
     * @var PluginConfigurationParser
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new PluginConfigurationParser();
    }

    #[Test] public function parseYamlReturnsPluginConfigurationInterfaceInstance(): void
    {
        $yamlContent = <<<YAML
plugin:
  extensionName: 'TestExtension'
  name: 'TestPlugin'
  type: 'CType'
  controllerActions:
    Test: 'list,show'
  nonCacheableControllerActions:
    Test: 'list'
YAML;

        $result = $this->subject->parseYaml($yamlContent);
        $this->assertInstanceOf(PluginConfigurationInterface::class, $result);
    }

    #[Test] public function parseYamlWithRegistrationReturnsPluginRegistrationInterfaceInstance(): void
    {
        $yamlContent = <<<YAML
plugin:
  extensionName: 'TestExtension'
  name: 'TestPlugin'
  type: 'CType'
  controllerActions:
    Test: 'list,show'
  nonCacheableControllerActions:
    Test: 'list'
registration:
  title: 'Test Plugin'
  description: 'Test Description'
  icon: 'test-icon'
  group: 'test'
  flexForm: 'EXT:test/flexform.xml'
YAML;

        $result = $this->subject->parseYaml($yamlContent);
        $this->assertInstanceOf(PluginRegistrationInterface::class, $result);
    }

    #[Test] public function parseYamlSetsCorrectPluginValues(): void
    {
        $yamlContent = <<<YAML
plugin:
  extensionName: 'TestExtension'
  name: 'TestPlugin'
  type: 'CType'
  controllerActions:
    Test: 'list,show'
  nonCacheableControllerActions:
    Test: 'list'
YAML;

        $result = $this->subject->parseYaml($yamlContent);

        $this->assertEquals('TestExtension', $result->getExtensionName());
        $this->assertEquals('TestPlugin', $result->getPluginName());
        $this->assertEquals(ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT, $result->getPluginType());
        $this->assertEquals(['Test' => 'list,show'], $result->getControllerActions());
        $this->assertEquals(['Test' => 'list'], $result->getNonCacheableControllerActions());
    }

    #[Test] public function parseYamlWithListTypePluginType(): void
    {
        $yamlContent = <<<YAML
plugin:
  extensionName: 'TestExtension'
  name: 'TestPlugin'
  type: 'list_type'
  controllerActions:
    Test: 'list,show'
YAML;

        $result = $this->subject->parseYaml($yamlContent);

        $this->assertEquals(ExtensionUtility::PLUGIN_TYPE_PLUGIN, $result->getPluginType());
    }

    #[Test] public function parseYamlSetsCorrectRegistrationValues(): void
    {
        $yamlContent = <<<YAML
plugin:
  extensionName: 'TestExtension'
  name: 'TestPlugin'
  type: 'CType'
  controllerActions:
    Test: 'list,show'
registration:
  title: 'Test Plugin'
  description: 'Test Description'
  icon: 'test-icon'
  group: 'test'
  flexForm: 'FILE:EXT:test/flexform.xml'
YAML;

        $result = $this->subject->parseYaml($yamlContent);

        $this->assertInstanceOf(PluginRegistrationInterface::class, $result);
        $this->assertEquals('Test Plugin', $result->getPluginTitle());
        $this->assertEquals('Test Description', $result->getPluginDescription());
        $this->assertEquals('test-icon', $result->getPluginIcon());
        $this->assertEquals('test', $result->getPluginGroup());
        $this->assertEquals('FILE:EXT:test/flexform.xml', $result->getFlexForm());
    }

    #[Test] public function parseYamlThrowsExceptionOnMissingPluginSection(): void
    {
        $this->expectException(\RuntimeException::class);

        $yamlContent = <<<YAML
registration:
  title: 'Test Plugin'
YAML;

        $this->subject->parseYaml($yamlContent);
    }
}
