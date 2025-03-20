<?php

namespace DWenzel\T3extensionTools\Traits\Upgrade;

use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

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
 * Trait UpgradeWizardTrait
 * Provides a convenient implementation of the accessor methods of the
 * @see UpgradeWizardInterface
 *
 * The included traits require some constants to be present in the consuming class
 */
trait UpgradeWizardTrait
{
    use DescriptionTrait;
    use IdentifierTrait;
    use PrerequisitesTrait;
    use TitleTrait;
}
