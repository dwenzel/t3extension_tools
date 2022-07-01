<?php

namespace DWenzel\T3extensionTools\Command;

use Symfony\Component\Console\Command\Command;

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
 * Class Status
 * This class provides wrapper methods for constants of the Command::class
 * which are not defined in those versions of the symfony package used in TYPO3 9
 * Use those static methods to get a correct status code instead.
 *
 */
class Status implements StatusAwareInterface
{
    public static function success(): int
    {
        return defined(Command::class . 'Command::class' . '::SUCCESS') ? Command::SUCCESS : self::SUCCESS;
    }

    public static function failure(): int
    {
        return defined(Command::class . 'Command::class' . '::FAILURE') ? Command::FAILURE : self::FAILURE;
    }

    public static function invalid(): int
    {
        return defined(Command::class . 'Command::class' . '::INVALID') ? Command::INVALID : self::INVALID;
    }
}
