<?php

namespace DWenzel\T3extensionTools\Service;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
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
 * Class PluginConfigurationService
 *
 * This class is responsible for configuring plugins in the CMS.
 */
class PluginConfigurationService
{
    use PluginProcessorTrait;

    /**
     * @param iterable<PluginConfigurationInterface> $plugins
     */
    public function __construct(
        protected iterable $plugins,
    )
    {
    }

    /**
     * Configure all plugins
     * This method is called in EXT:t3extension-tools/ext_localconf.php
     * You must not call it in your extension
     *
     * For each plugin a class implementing the PluginConfigurationInterface must exist
     */
    public function configurePlugins(): void
    {
        $this->processPlugins(
            $this->plugins,
            function ($plugin) {
            ExtensionUtility::configurePlugin(
                    $plugin->getExtensionName(),
                    $plugin->getPluginName(),
                    $plugin->getControllerActions(),
                    $plugin->getNonCacheableControllerActions(),
                    $plugin->getPluginType(),
                );
            },
            PluginConfigurationInterface::class
            );
        }
    }

