<?php

namespace DWenzel\T3extensionTools\Service;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\SingletonInterface;
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
 * Class YamlPluginRegistrationService
 *
 * This class registers TYPO3 plugins from YAML configuration files.
 */
class YamlPluginRegistrationService implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    
    /**
     * @var PluginConfigurationScanner
     */
    protected PluginConfigurationScanner $scanner;

    /**
     * @var PluginConfigurationParser
     */
    protected PluginConfigurationParser $parser;
    
    /**
     * @var PackageManager
     */
    protected PackageManager $packageManager;

    public function __construct(
        PluginConfigurationScanner $scanner,
        PluginConfigurationParser $parser,
        PackageManager $packageManager
    ) {
        $this->scanner = $scanner;
        $this->parser = $parser;
        $this->packageManager = $packageManager;
    }

    /**
     * Configure all plugins from YAML files
     */
    public function configurePlugins(): void
    {
        $pluginConfigurations = $this->findAndParsePluginConfigurations();
        
        foreach ($pluginConfigurations as $config) {
            ExtensionUtility::configurePlugin(
                $config->getExtensionName(),
                $config->getPluginName(),
                $config->getControllerActions(),
                $config->getNonCacheableControllerActions(),
                $config->getPluginType()
            );
        }
    }

    /**
     * Register all plugins from YAML files
     */
    public function registerPlugins(): void
    {
        $pluginConfigurations = $this->findAndParsePluginConfigurations();
        
        foreach ($pluginConfigurations as $config) {
            if ($config instanceof PluginRegistrationInterface) {
                ExtensionUtility::registerPlugin(
                    $config->getExtensionName(),
                    $config->getPluginName(),
                    $config->getPluginTitle(),
                    $config->getPluginIcon(),
                    $config->getPluginGroup()
                );
                
                // Add flexform if specified
                if (!empty($config->getFlexForm())) {
                    $listType = strtolower($config->getExtensionName() . '_' . GeneralUtility::camelCaseToLowerCaseUnderscored($config->getPluginName()));
                    
                    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$listType] = 'pi_flexform';
                    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$listType] = 'layout,select_key,pages,recursive';
                    
                    if (substr($config->getFlexForm(), 0, 5) === 'FILE:') {
                        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds'][$listType] = $config->getFlexForm();
                    } else {
                        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds'][$listType] = $config->getFlexForm();
                    }
                }
            }
        }
    }

    /**
     * Find and parse all plugin configurations from YAML files
     *
     * @return PluginConfigurationInterface[]|PluginRegistrationInterface[] Array of objects implementing PluginConfigurationInterface
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    protected function findAndParsePluginConfigurations(): array
    {
        $pluginConfigurations = [];
        $extensionsWithPlugins = $this->scanner->findExtensionsWithPluginConfigurations();
        
        foreach ($extensionsWithPlugins as $extensionKey => $pluginFiles) {
            foreach ($pluginFiles as $fileName) {
                $filePath = $this->scanner->getPluginConfigurationPath($extensionKey, $fileName);
                try {
                    $pluginConfig = $this->parser->parseFile($filePath);
                    $pluginConfigurations[] = $pluginConfig;
                } catch (\Exception $e) {
                    if ($this->logger) {
                        $this->logger->error(
                            'Error parsing plugin configuration file: ' . $filePath,
                            [
                                'exception' => $e->getMessage(),
                                'extension' => $extensionKey,
                                'file' => $fileName
                            ]
                        );
                    }
                }
            }
        }
        
        return $pluginConfigurations;
    }
}