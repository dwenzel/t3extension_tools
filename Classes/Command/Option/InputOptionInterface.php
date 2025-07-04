<?php

namespace DWenzel\T3extensionTools\Command\Option;

use DWenzel\T3extensionTools\Traits\Command\ConfigureTrait;

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
 * Interface InputOptionInterface
 *
 * Describes command options for registration.
 * @see ExampleOption for usage
 * @see ConfigureTrait::configure() for implementation
 */
interface InputOptionInterface
{
    public static function name(): string;

    public static function description(): string;

    public static function shortCut(): string;

    public static function mode(): ?int;

    public static function defaultValue(): mixed;
}
