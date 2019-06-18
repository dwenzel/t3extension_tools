<?php
namespace DWenzel\T3extensionTools\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use DWenzel\T3extensionTools\Configuration\EmConfiguration;
use DWenzel\T3extensionTools\Configuration\SettingsInterface as SI;

/**
 * Class EmConfigurationUtility
 */
class EmConfigurationUtility
{

    /**
     * Gets the settings from extension manager
     *
     * @return EmConfiguration
     * @throws \BadFunctionCallException
     */
    public static function getSettings(): EmConfiguration
    {
        $configuration = self::parseSettings();
        require_once ExtensionManagementUtility::extPath(SI::EXTENSION_KEY) . 'Classes/Configuration/EmConfiguration.php';
        return new EmConfiguration($configuration);
    }

    /**
     * Parse settings and return it as array
     *
     * @return array un-serialized settings from extension manager
     */
    public static function parseSettings()
    {
        $settings = [];

        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][SI::EXTENSION_KEY])) {
            $settings = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][SI::EXTENSION_KEY], ['allowed_classes' => false]);
        }

        return $settings;
    }
}
