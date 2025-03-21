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
interface ModuleRegistrationInterface
{
    public const EXTENSION_NAME = 'ExtensionName';
    public const MAIN_MODULE_NAME = 'mainModuleName';
    public const MODULE_CONFIGURATION = 'moduleConfiguration';
    public const POSITION = 'position';
    public const SUB_MODULE_NAME = 'subModuleName';
    public const CONTROLLER_ACTIONS = [];
    public const NON_CACHEABLE_CONTROLLER_ACTIONS = [];

    /**
     * Get the name of the submodule to register
     *
     * @return string
     */
    public static function getSubmoduleName(): string;

    /**
     * Get the name of the main module in
     * which the submodule shall appear
     *
     * @return string
     */
    public static function getMainModuleName(): string;

    /**
     * Get the position in the main module where the
     * submodule shall appear
     * Allowed: top, bottom, before:<nameOfModule>, after:<nameOfModule>
     * @return string
     */
    public static function getPosition(): string;

    /**
     * Get an array of configuration for the module
     *
     * @return array
     */
    public static function getModuleConfiguration(): array;
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
