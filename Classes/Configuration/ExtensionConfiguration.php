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
     * TsConfig files to register with registerPageTSConfigFile IconRegistry
     * [
     *  <pathToTsConfigFile> => <label>
     * ]
     * @var string[]
     */
    protected const REGISTER_PAGE_TSCONFIG_FILES = [];

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
     * Register TsConfig files
     */
    public static function registerPageTSConfigFiles(): void
    {
        foreach (static::REGISTER_PAGE_TSCONFIG_FILES as $TsConfigFile => $label) {
            ExtensionManagementUtility::registerPageTSConfigFile(static::EXTENSION_KEY, $TsConfigFile, $label);
        }
    }
}
