<?php

namespace DWenzel\T3extensionTools\Command\Option;

use DWenzel\T3extensionTools\Traits\Command\Option\InputOptionTrait;
use Symfony\Component\Console\Input\InputOption;

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
 * Class Example
 */
class ExampleOption implements InputOptionInterface
{
    use InputOptionTrait;

    public const NAME = 'exampleOption';
    public const HELP = 'an example option for an example command';
    public const MODE = InputOption::VALUE_REQUIRED;
    public const DESCRIPTION = 'example option';
    public const SHORTCUT = 'eOpt';
    public const DEFAULT = null;
}
