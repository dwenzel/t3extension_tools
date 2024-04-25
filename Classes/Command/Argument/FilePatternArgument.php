<?php

namespace DWenzel\T3extensionTools\Command\Argument;

use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use DWenzel\T3extensionTools\Traits\Command\Argument\InputArgumentTrait;
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
 * Class FilePatternArgument
 *
 * @example
 *
 * ### default TYPO3 log files
 * pattern "/^typo3_deprecations_([0-9a-z]{10})\.log/" will match any
 * file name beginning with "typo3_deprecations_" followed by exactly 10 alphanumeric characters and
 * with an extension ".log"
 *
 * ### files with leading date string
 * pattern "/
 */
 class FilePatternArgument implements InputArgumentInterface
{
    use InputArgumentTrait;

    public const NAME = 'pattern';
    public const HELP = 'Provide a pattern for files to delete. Pattern must not contain the path.';
    public const MODE = InputOption::VALUE_REQUIRED;
    public const DESCRIPTION = 'pattern for log file';
    public const SHORTCUT = 'p';
    public const DEFAULT = null;
}
