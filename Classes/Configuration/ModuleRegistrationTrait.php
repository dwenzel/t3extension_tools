<?php

namespace DWenzel\T3extensionTools\Configuration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Dirk Wenzel <wenzel@cps-it.de>
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
use DWenzel\T3extensionTools\Configuration\ModuleRegistrationInterface as MCI;

trait ModuleRegistrationTrait
{
    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public static function getVendorExtensionName(): string
    {
        return self::getStaticProperty(MCI::VENDOR_EXTENSION_NAME);
    }
    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public static function getSubmoduleName(): string
    {
        return self::getStaticProperty(MCI::SUB_MODULE_NAME);
    }

    /**
     * @return array
     * @throws InvalidConfigurationException
     */
    public static function getControllerActions(): array
    {
        return self::getStaticProperty(MCI::CONTROLLER_ACTIONS);
    }

    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public static function getMainModuleName(): string
    {
        return self::getStaticProperty(MCI::MAIN_MODULE_NAME);
    }

    /**
     * @return array
     * @throws InvalidConfigurationException
     */
    public static function getModuleConfiguration(): array
    {
        return self::getStaticProperty(MCI::MODULE_CONFIGURATION);
    }

    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public static function getPosition(): string
    {
        return self::getStaticProperty(MCI::POSITION);
    }

    /**
     * @param string $propertyName
     * @return mixed
     * @throws InvalidConfigurationException
     */
    protected static function getStaticProperty(string $propertyName)
    {
        if (property_exists(__CLASS__, $propertyName)) {
            return static::$$propertyName;
        }
        throw new InvalidConfigurationException(
            "Missing property $propertyName in class".  __CLASS__. ".",
            1565600918
        );
    }
}
