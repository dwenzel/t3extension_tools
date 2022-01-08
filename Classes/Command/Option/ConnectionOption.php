<?php

namespace DWenzel\T3extensionTools\Command\Option;

use DWenzel\T3extensionTools\Traits\Command\Option\InputOptionTrait;
use Symfony\Component\Console\Input\InputOption;
use TYPO3\CMS\Core\Database\ConnectionPool;

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
class ConnectionOption implements InputOptionInterface
{
    use InputOptionTrait;

    public const NAME = 'connection';
    public const HELP = 'Identifier of the database connection which shall be used. Defaults to default TYPO3 connection';
    public const MODE = InputOption::VALUE_OPTIONAL;
    public const DESCRIPTION = 'connection identifier';
    public const SHORTCUT = 'c';
    public const DEFAULT = ConnectionPool::DEFAULT_CONNECTION_NAME;


}
