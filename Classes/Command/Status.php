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
 * Wrapper methods for the status constants of Symfony's Command class,
 * which were missing in the Symfony versions used by TYPO3 9.
 *
 * @deprecated since 7.0, will be removed in 8.0. Use Command::SUCCESS, Command::FAILURE and Command::INVALID.
 */
class Status implements StatusAwareInterface
{
    public static function success(): int
    {
        self::triggerDeprecation('success');
        return Command::SUCCESS;
    }

    public static function failure(): int
    {
        self::triggerDeprecation('failure');
        return Command::FAILURE;
    }

    public static function invalid(): int
    {
        self::triggerDeprecation('invalid');
        return Command::INVALID;
    }

    private static function triggerDeprecation(string $method): void
    {
        trigger_error(
            self::class . '::' . $method . '() is deprecated since t3extension_tools 7.0 and will be removed in 8.0.'
            . ' Use ' . Command::class . '::' . strtoupper($method) . ' instead.',
            E_USER_DEPRECATED
        );
    }
}
