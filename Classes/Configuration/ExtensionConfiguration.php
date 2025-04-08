<?php

namespace DWenzel\T3extensionTools\Configuration;

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconProviderInterface;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
    /**
     * @deprecated  use UPDATE_WIZARDS_TO_REGISTER instead
     */
    public const UPDATE_WIZARDS = [];

    /**
     * Array of Update wizard to register during extension configuration.
     * [
     *   <Identifier> => <Fully Qualified Class Name>
     * ]
     */
    public const UPDATE_WIZARDS_TO_REGISTER = [];
    /**
     * [
     *  <tableName>,
     *  <otherTable>
     * ]
     */
    public const TABLES_ALLOWED_ON_STANDARD_PAGES = [];
    /**
     * [
     *  <tableName:<pathToLocalizedHelpFile>
     * ]
     */
    public const LOCALIZED_TABLE_DESCRIPTION = [];

    /**
     * Bitmap icons to register with IconRegistry
     * [
     *  <identifier> = <pathToIcon>
     * ]
     */
    protected const BITMAP_ICONS_TO_REGISTER = [];

    /**
     * SVG icons to register with IconRegistry
     * [
     *  <identifier> = <pathToIcon>
     * ]
     */
    protected const SVG_ICONS_TO_REGISTER = [];

    /**
     * Array of strings to add as TSconfig content.
     * [
     *  <Page TsConfig string to add>
     * ]
     * @var string[]
     */
    protected const ADD_PAGE_TSCONFIG = [];

    /**
     * TsConfig files to register with registerPageTSConfigFile IconRegistry
     * [
     *  <pathToTsConfigFile> => <label>
     * ]
     * @var string[]
     */
    protected const REGISTER_PAGE_TSCONFIG_FILES = [];

    /**
     * Register update wizards
     */
    public static function registerUpdateWizards(): void
    {
        foreach (static::UPDATE_WIZARDS as $class) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][$class]
                = $class;
        }

        foreach (static::UPDATE_WIZARDS_TO_REGISTER as $identifier => $class) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][$identifier]
                = $class;
        }
    }

    /**
     * Register icons
     * Icons must be configured in ExtensionConfiguration
     * constants BITMAP_ICONS_TO_REGISTER and SVG_ICONS_TO_REGISTER
     * @throws InvalidConfigurationException
     */
    public static function registerIcons(): void
    {
        self::registerIconsWithProvider(
            static::BITMAP_ICONS_TO_REGISTER,
            BitmapIconProvider::class
        );
        self::registerIconsWithProvider(
            static::SVG_ICONS_TO_REGISTER,
            SvgIconProvider::class
        );
    }

    /**
     * Registers icons with a provider class
     * @param array $icons
     * @param string $iconProviderClass
     * @throws InvalidConfigurationException
     */
    protected static function registerIconsWithProvider(array $icons, string $iconProviderClass): void
    {
        if (empty($icons)) {
            return;
        }
        if (!in_array(IconProviderInterface::class, class_implements($iconProviderClass), true)) {
            throw new InvalidConfigurationException(
                "Invalid IconProvider '$iconProviderClass'. Provider class must implement " .
                IconProviderInterface::class,
                1_565_689_093
            );
        }
        $registry = GeneralUtility::makeInstance(IconRegistry::class);
        foreach (static::SVG_ICONS_TO_REGISTER as $identifier => $path) {
            $registry->registerIcon(
                $identifier,
                $iconProviderClass,
                ['source' => $path]
            );
        }
    }

    /**
     * @deprecated allowTablesOnStandardPages will be removed in TYPO3 v13.0. Use $GLOBALS['TCA'][$table]['ctrl']['security']['ignorePageTypeRestriction'] instead.
     * @deprecated addLocalizedTableDescriptionThe functionality has been removed in v12. The method will be removed in TYPO3 v13.
     */
    public static function configureTables(): void
    {
        self::allowTablesOnStandardPages();
        self::addLocalizedTableDescription();
    }

    /**
     * @deprecated will be removed in TYPO3 v13.0. Use $GLOBALS['TCA'][$table]['ctrl']['security']['ignorePageTypeRestriction'] instead.
     */
    protected static function allowTablesOnStandardPages(): void
    {
        foreach (static::TABLES_ALLOWED_ON_STANDARD_PAGES as $table) {
            ExtensionManagementUtility::allowTableOnStandardPages($table);
        }
    }

    /**
     * @deprecated functionality has been removed in v12. The method will be removed in TYPO3 v13.
     */
    protected static function addLocalizedTableDescription(): void
    {
        foreach (static::LOCALIZED_TABLE_DESCRIPTION as $table => $file) {
        }
    }

    /**
     * Add page TSconfig content
     */
    public static function addPageTSconfig(): void
    {
        foreach (static::ADD_PAGE_TSCONFIG as $TsConfig) {
            ExtensionManagementUtility::addPageTSConfig($TsConfig);
        }
    }

    /**
     * Register TsConfig files
     */
    public static function registerPageTSConfigFiles(): void
    {
        foreach (static::REGISTER_PAGE_TSCONFIG_FILES as $TsConfigFile => $label) {
            ExtensionManagementUtility::registerPageTSConfigFile(static::KEY, $TsConfigFile, $label);
        }
    }
}
