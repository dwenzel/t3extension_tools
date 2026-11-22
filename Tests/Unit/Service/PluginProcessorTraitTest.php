<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Service;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginConfigurationTrait;
use DWenzel\T3extensionTools\Service\PluginProcessorTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProcessorTestPlugin implements PluginConfigurationInterface
{
    use PluginConfigurationTrait;

    protected string $extensionName = 'Test';
    protected string $pluginName = 'Plugin';
    protected string $pluginType = 'CType';
    /** @var array<string, string> */
    protected array $controllerActions = [];
    /** @var array<string, string> */
    protected array $nonCacheableControllerActions = [];
}

class PluginProcessorTraitTest extends TestCase
{
    /**
     * @return array<string, array{list<mixed>, list<class-string>}>
     */
    public static function pluginsDataProvider(): array
    {
        return [
            'no plugins' => [[], []],
            'object implementing the interface' => [[new ProcessorTestPlugin()], [ProcessorTestPlugin::class]],
            'class name implementing the interface' => [[ProcessorTestPlugin::class], [ProcessorTestPlugin::class]],
            'object not implementing the interface' => [[new \stdClass()], []],
            'class name not implementing the interface' => [[\stdClass::class], []],
            'unknown class name' => [['Unknown\\PluginClass'], []],
            'mixed' => [
                [new \stdClass(), ProcessorTestPlugin::class, 'Unknown\\PluginClass', new ProcessorTestPlugin()],
                [ProcessorTestPlugin::class, ProcessorTestPlugin::class],
            ],
        ];
    }

    /**
     * @param list<mixed> $plugins
     * @param list<class-string> $expected
     */
    #[Test]
    #[DataProvider('pluginsDataProvider')]
    public function processPluginsCallsCallbackForPluginsImplementingInterface(array $plugins, array $expected): void
    {
        $processed = [];
        $subject = new class {
            use PluginProcessorTrait {
                processPlugins as public;
            }
        };

        $subject->processPlugins(
            $plugins,
            static function (object $plugin) use (&$processed): void {
                $processed[] = $plugin::class;
            },
            PluginConfigurationInterface::class
        );

        self::assertSame($expected, $processed);
    }

    #[Test]
    public function processPluginsPassesTheGivenInstance(): void
    {
        $plugin = new ProcessorTestPlugin();
        $processed = null;
        $subject = new class {
            use PluginProcessorTrait {
                processPlugins as public;
            }
        };

        $subject->processPlugins(
            [$plugin],
            static function (object $object) use (&$processed): void {
                $processed = $object;
            },
            PluginConfigurationInterface::class
        );

        self::assertSame($plugin, $processed);
    }
}
