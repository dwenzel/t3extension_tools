<?php
namespace DWenzel\T3extensionTools\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3extensionTools\Configuration\SettingsInterface as SI;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\ExtensionService as FrameworkExtensionService;

/**
 * Class ExtensionService
 */
class ExtensionService extends FrameworkExtensionService
{
    /**
     * Checks if the given action is cacheable or not.
     * Method from parent class overwritten to allow evaluation of plugin setting 'notCacheable'.
     * Thus an editor is able to force an non caching behavior of the plugin.
     *
     * @param string $extensionName Name of the target extension, without underscores
     * @param string $pluginName Name of the target plugin
     * @param string $controllerName Name of the target controller
     * @param string $actionName Name of the action to be called
     * @return boolean TRUE if the specified plugin action is cacheable, otherwise FALSE
     */
    public function isActionCacheable($extensionName, $pluginName, $controllerName, $actionName)
    {
        if (!parent::isActionCacheable($extensionName, $pluginName, $controllerName, $actionName)) {
            return false;
        }

        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            $extensionName,
            $pluginName
        );
        return
            !(isset($frameworkConfiguration[SI::SETTINGS][SI::CACHE][SI::NOT_CACHEABLE]) &&
                (bool)$frameworkConfiguration[SI::SETTINGS][SI::CACHE][SI::NOT_CACHEABLE]);
    }
}
