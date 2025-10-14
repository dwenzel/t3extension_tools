<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Traits\Upgrade;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2022 Dirk Wenzel <wenzel@cps-it.de>
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
 * Trait PrerequisitesTrait
 *
 * Provides getPrerequisites() method implementation for UpgradeWizardInterface.
 * Requires PRE_REQUISITES constant to be defined in the using class.
 */
trait PrerequisitesTrait
{
    /**
     * Returns an array of class names of Prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[] Array of prerequisite class names from PRE_REQUISITES constant
     */
    public function getPrerequisites(): array
    {
        return static::PRE_REQUISITES;
    }
}
