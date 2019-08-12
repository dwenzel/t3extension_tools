<?php

namespace DWenzel\T3extensionTools\Configuration;

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Dirk Wenzel
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
class ExtensionConfiguration
{
    public const EXTENSION_KEY = 't3extension_tools';
    public const UPDATE_WIZARDS = [];

    /**
     * Configuration class names for module registration
     * Class must implement ModuleRegistrationInterface
     */
    protected const MODULES_TO_REGISTER = [];

    public static function registerUpdateWizards(): void
    {
        foreach (static::UPDATE_WIZARDS as $class) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][$class]
                = $class;
        }

    }

    /**
     * Register custom modules or reconfigure existing modules
     * for the backend
     * Overwrite this method if necessary
     */
    public static function registerAndConfigureModules()
    {
        foreach (static::MODULES_TO_REGISTER as $module) {
            if (!in_array(ModuleRegistrationInterface::class, class_implements($module), true)) {
                echo $module . ' wrong instance';
                continue;
            }
            ExtensionUtility::registerModule(
                $module::getVendorExtensionName(),
                $module::getMainModuleName(),
                $module::getSubmoduleName(),
                $module::getPosition(),
                $module::getControllerActions(),
                $module::getModuleConfiguration()
            );
        }
    }

    /**
     * override in order to reconfigure tables
     */
    public static function reconfigureTables()
    {
    }
}
