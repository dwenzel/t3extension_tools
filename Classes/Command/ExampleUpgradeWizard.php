<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Command;

use DWenzel\T3extensionTools\Traits\Upgrade\UpgradeWizardTrait;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

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
 * Example Upgrade Wizard
 *
 * Demonstrates the usage of upgrade wizard traits provided by t3extension-tools.
 * This class serves as an example implementation and prevents PHPStan from
 * reporting unused traits.
 *
 * Usage in your extension:
 * 1. Copy this class to your extension
 * 2. Update the constants (TITLE, DESCRIPTION, PRE_REQUISITES)
 * 3. Implement the executeUpdate() and updateNecessary() methods
 * 4. Register the wizard in ext_localconf.php
 */
final class ExampleUpgradeWizard implements UpgradeWizardInterface
{
    use UpgradeWizardTrait;

    /**
     * The wizard title shown in the upgrade wizard list
     */
    public const TITLE = 'T3extension Tools: Example Upgrade Wizard';

    /**
     * The wizard description explaining what it does
     */
    public const DESCRIPTION = 'This is an example upgrade wizard demonstrating the usage of upgrade wizard traits. ' .
        'It shows how to implement a TYPO3 upgrade wizard using the reusable components from t3extension-tools.';

    /**
     * Prerequisites that must be fulfilled before this wizard can run
     *
     * @var string[]
     */
    public const PRE_REQUISITES = [
        DatabaseUpdatedPrerequisite::class,
    ];

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     */
    public function executeUpdate(): bool
    {
        // Example implementation - replace with your actual upgrade logic
        // For example: migrate data, update records, etc.

        return true;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     */
    public function updateNecessary(): bool
    {
        // Example implementation - replace with your actual check logic

        // Check if upgrade is needed
        // For example: check if old data exists, configuration needs update, etc.

        // In this example, we always return false to prevent accidental execution
        return false;
    }
}
