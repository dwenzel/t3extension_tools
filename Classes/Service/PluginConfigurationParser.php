<?php

namespace DWenzel\T3extensionTools\Service;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2025 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class PluginConfigurationParser
 *
 * This class parses YAML plugin configuration files.
 */
class PluginConfigurationParser
{
    /**
     * Parse a YAML plugin configuration file and return a plugin configuration object
     *
     * @param string $filePath The path to the YAML file
     * @return object An object implementing PluginConfigurationInterface and PluginRegistrationInterface
     * @throws \RuntimeException If the file is not valid or missing required fields
     */
    public function parseFile(string $filePath): object
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('Plugin configuration file not found: %s', $filePath));
        }

        $content = file_get_contents($filePath);
        return $this->parseYaml($content);
    }

    /**
     * Parse YAML content and return a plugin configuration object
     *
     * @param string $yamlContent The YAML content
     * @return object An object implementing PluginConfigurationInterface and PluginRegistrationInterface
     * @throws \RuntimeException If the content is not valid or missing required fields
     */
    public function parseYaml(string $yamlContent): object
    {
        $config = Yaml::parse($yamlContent);
        
        // Validate required sections
        if (!isset($config['plugin'])) {
            throw new \RuntimeException('Plugin configuration must contain a "plugin" section');
        }
        
        // Create anonymous class implementing the required interfaces
        return new class($config) implements PluginConfigurationInterface, PluginRegistrationInterface {
            protected array $config;
            protected string $extensionName;
            protected string $pluginName;
            protected string $pluginType;
            protected array $controllerActions;
            protected array $nonCacheableControllerActions;
            protected string $pluginTitle = '';
            protected string $pluginDescription = '';
            protected string $pluginIcon = '';
            protected string $pluginGroup = '';
            protected string $flexForm = '';

            public function __construct(array $config)
            {
                $this->config = $config;
                $this->extensionName = $config['plugin']['extensionName'] ?? '';
                $this->pluginName = $config['plugin']['name'] ?? '';
                $this->pluginType = $config['plugin']['type'] ?? ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT;
                $this->controllerActions = $this->parseControllerActions($config['plugin']['controllerActions'] ?? []);
                $this->nonCacheableControllerActions = $this->parseControllerActions($config['plugin']['nonCacheableControllerActions'] ?? []);
                
                // Registration config (optional)
                if (isset($config['registration'])) {
                    $this->pluginTitle = $config['registration']['title'] ?? '';
                    $this->pluginDescription = $config['registration']['description'] ?? '';
                    $this->pluginIcon = $config['registration']['icon'] ?? '';
                    $this->pluginGroup = $config['registration']['group'] ?? '';
                    $this->flexForm = $config['registration']['flexForm'] ?? '';
                }
            }

            protected function parseControllerActions(array $actions): array
            {
                $parsed = [];
                foreach ($actions as $controller => $actionList) {
                    $parsed[$controller] = $actionList;
                }
                return $parsed;
            }

            public function getExtensionName(): string
            {
                return $this->extensionName;
            }

            public function getPluginName(): string
            {
                return $this->pluginName;
            }

            public function getPluginType(): string
            {
                return $this->pluginType;
            }

            public function getControllerActions(): array
            {
                return $this->controllerActions;
            }

            public function getNonCacheableControllerActions(): array
            {
                return $this->nonCacheableControllerActions;
            }

            public function getPluginTitle(): string
            {
                return $this->pluginTitle;
            }

            public function getPluginDescription(): string
            {
                return $this->pluginDescription;
            }

            public function getPluginIcon(): string
            {
                return $this->pluginIcon;
            }

            public function getPluginGroup(): string
            {
                return $this->pluginGroup;
            }

            public function getFlexForm(): string
            {
                return $this->flexForm;
            }
        };
    }
}