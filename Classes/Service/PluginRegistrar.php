<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2026 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Registers a plugin as content type in TCA, including its FlexForm.
 * Used for plugins defined by PHP classes and by YAML files.
 * Must be called from Configuration/TCA/Overrides.
 */
class PluginRegistrar
{
    public const DEFAULT_ICON = 'content-plugin';
    public const DEFAULT_GROUP = 'plugins';
    public const FLEX_FORM_TAB = '--div--;Configuration,pi_flexform,';

    /**
     * @return string The plugin signature (CType value)
     */
    public function register(PluginRegistrationInterface $plugin): string
    {
        $flexForm = $plugin->getFlexForm();
        $arguments = [
            $plugin->getExtensionName(),
            $plugin->getPluginName(),
            $plugin->getPluginTitle(),
            $plugin->getPluginIcon() ?: self::DEFAULT_ICON,
            $plugin->getPluginGroup() ?: self::DEFAULT_GROUP,
            $plugin->getPluginDescription(),
        ];

        if ($this->isFlexFormHandledByCore()) {
            // TYPO3 14+: registerPlugin() adds the data structure and the FlexForm tab
            return ExtensionUtility::registerPlugin(...[...$arguments, $flexForm]);
        }

        $signature = ExtensionUtility::registerPlugin(...$arguments);
        if ($flexForm !== '') {
            ExtensionManagementUtility::addToAllTCAtypes('tt_content', self::FLEX_FORM_TAB, $signature, 'after:subheader');
            ExtensionManagementUtility::addPiFlexFormValue('*', $flexForm, $signature);
        }

        return $signature;
    }

    protected function isFlexFormHandledByCore(): bool
    {
        return (new Typo3Version())->getMajorVersion() >= 14;
    }
}
