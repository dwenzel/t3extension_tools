<?php

namespace DWenzel\T3extensionTools\Command\Argument;

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
class DirectoryArgument implements InputArgumentInterface
{
    use InputArgumentTrait;

    public const NAME = 'directory';
    public const HELP = 'Provide a directory path for files to delete.';
    public const MODE = InputOption::VALUE_REQUIRED;
    public const DESCRIPTION = 'directory path for log files';
    public const SHORTCUT = 'd';
    public const DEFAULT = 'var/log';
}
