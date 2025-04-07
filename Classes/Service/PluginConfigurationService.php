<?php

namespace DWenzel\T3extensionTools\Service;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
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
class PluginConfigurationService
{
    /**
     * @param iterable<PluginConfigurationInterface> $pluginsToConfigure
     * @param iterable<PluginRegistrationInterface> $pluginsToRegister
     */
    public function __construct(
        protected iterable $pluginsToConfigure,
        protected iterable $pluginsToRegister,
    )
    {
    }

    /**
     * Register all plugins
     *
     * For each plugin a class implementing the PluginRegistrationInterface must exist
     * This class must be tagged for dependency injection with 't3extensionTools.pluginConfiguration'
     * */
    public function registerPlugins(): void
    {
        foreach ($this->pluginsToRegister as $pluginRegistrationClass) {
            if (!in_array(PluginRegistrationInterface::class, class_implements($pluginRegistrationClass), true)) {
                continue;
            }

            /** @var PluginRegistrationInterface $pluginRegistration */
            $pluginRegistration = new $pluginRegistrationClass();

            $pluginSignature = ExtensionUtility::registerPlugin(
                $pluginRegistration->getExtensionName(),
                $pluginRegistration->getPluginName(),
                $pluginRegistration->getPluginTitle(),
                $pluginRegistration->getPluginIcon(),
                $pluginRegistration->getPluginGroup(),
                $pluginRegistration->getPluginDescription(),
            );

            if (!empty($flexForm = $pluginRegistration->getFlexForm())) {
                $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
                ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, $flexForm);
            }
        }
    }

    /**
     * Configure all plugins
     * For each plugin a class implementing the PluginConfigurationInterface must exist
     * This class must be tagged for dependency injection with 't3extensionTools.pluginConfiguration'
     */
    public function configurePlugins(): void
    {
        foreach ($this->pluginsToConfigure as $configurationClass) {
            if (!in_array(PluginConfigurationInterface::class, class_implements($configurationClass), true)) {
                continue;
            }

            /** @var PluginConfigurationInterface $configurationClass */
            $pluginConfiguration = new $configurationClass();

            ExtensionUtility::configurePlugin(
                $pluginConfiguration->getExtensionName(),
                $pluginConfiguration->getPluginName(),
                $pluginConfiguration->getControllerActions(),
                $pluginConfiguration->getNonCacheableControllerActions(),
                $pluginConfiguration->getPluginType(),
            );
        }
    }


}
