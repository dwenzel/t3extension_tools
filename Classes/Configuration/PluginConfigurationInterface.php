<?php

namespace DWenzel\T3extensionTools\Configuration;

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

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
interface PluginConfigurationInterface
{
    public const EXTENSION_NAME = 'extensionName';
    /**
     * Name of the property holding the plugin name
     */
    public const PLUGIN_NAME = 'pluginName';
    /**
     * Name of the property holding the plugin title
     */
    public const PLUGIN_TITLE = 'pluginTitle';
    public const PLUGIN_ICON = 'pluginIcon';
    public const PLUGIN_GROUP = 'plugin';
    public const PLUGIN_TYPE = 'pluginType';

    /**
     * Name of the property holding the flex form
     */
    public const FLEX_FORM = 'flexForm';
    public const CONTROLLER_ACTIONS = 'controllerActions';
    public const NON_CACHEABLE_CONTROLLER_ACTIONS = 'nonCacheableControllerActions';

    /**
     * Get the name of the plugin to register
     *
     * @return string
     */
    public static function getPluginName(): string;

    /**
     * Get the title of the plugin to register
     * This can be a localized string reference
     * @return string
     */
    public static function getPluginTitle(): string;

    /**
     * Get the icon of the plugin to register
     * @return string
     */
    public static function getPluginIcon(): string;

    /**
     * Get the group of the plugin to register
     * defualt is 'plugin'
     * @return string
     */
    public static function getPluginGroup(): string;

    /**
     * Get the group of the plugin to register
     * defualt is 'ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT'
     * @return string
     */
    public static function getPluginType(): string;

    /**
     * Get the flex form configuration for the plugin to register
     * @return string
     */
    public static function getFlexForm(): string;

    /**
     * Get an array of controller action combinations in the
     * form:
     * [
     *   <ControllerName> => '<actionName>,<otherActionName>'
     * ]
     * (omit the 'Action' part from the method name)
     *
     * @return array
     */
    public static function getControllerActions(): array;

    /**
     * Get the Controller/Actions which shall not be cached
     * [
     *  <ControllerName> => <actionName>
     * ]
     * Omit the 'Action' part form action methods
     *
     * @return array
     */
    public static function getNonCacheableControllerActions(): array;

    /**
     * Get a key for registration in the form of
     * <extensionName>
     *
     * @return string
     */
    public static function getExtensionName(): string;
}
