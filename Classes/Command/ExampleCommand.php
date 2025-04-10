<?php

namespace DWenzel\T3extensionTools\Command;

use DWenzel\T3extensionTools\Command\Argument\ExampleArgument;
use DWenzel\T3extensionTools\Command\Option\ConnectionOption;
use DWenzel\T3extensionTools\Command\Option\ExampleOption;
use DWenzel\T3extensionTools\Traits\Command\ArgumentAwareTrait;
use DWenzel\T3extensionTools\Traits\Command\ConfigureTrait;
use DWenzel\T3extensionTools\Traits\Command\ExecuteSqlTrait;
use DWenzel\T3extensionTools\Traits\Command\InitializeTrait;
use DWenzel\T3extensionTools\Traits\Command\OptionAwareTrait;
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
class ExampleCommand extends Command implements ArgumentAwareInterface, OptionAwareInterface
{
    use ArgumentAwareTrait;
    use ConfigureTrait;
    use InitializeTrait;
    use OptionAwareTrait;
    use ExecuteSqlTrait;

    public const string DEFAULT_NAME = 't3extension-tools:example';
    public const string SQL_FILE_PATH = 'EXT:t3extension_tools/Resources/Private/SQL/example.sql';
    public const string MESSAGE_DESCRIPTION_COMMAND = 'Example command';
    public const string MESSAGE_HELP_COMMAND = 'Does nothing';
    public const string MESSAGE_SUCCESS = 'successfully done nothing';
    public const string OPTION_CONNECTION = ConnectionOption::NAME;
    public const string OPTION_EXAMPLE = ExampleOption::NAME;
    public const string OPTION_CONNECTION_DEFAULT = 'Default';
    public const string CONNECTION_TYPE_MYSQL = 'mysql';
    public const string ERROR_MISSING_CONNECTION = 'No suitable MySQL connection found.';
    public const string ERROR_SQL_EXECUTION_FAILED = 'Execution of SQL statement failed';
    public const string MESSAGE_STARTING = 'Start doing nothing...';
    public const array DEFAULT_MYSQL_ARGUMENTS = ['--skip-column-names'];

    /**
     * @var string
     */
    protected static string $defaultName = self::DEFAULT_NAME;

    protected const array OPTIONS = [
        ExampleOption::class,
        ConnectionOption::class,
    ];

    protected const array ARGUMENTS = [
        ExampleArgument::class,
    ];

    /**
     * @var array|string[]
     */
    protected static array $optionsToConfigure = self::OPTIONS;

    /**
     * @var array|string[]
     */
    protected static array $argumentsToConfigure = self::ARGUMENTS;
}
