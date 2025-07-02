<?php

namespace DWenzel\T3extensionTools\Traits\Command;

use Helhum\Typo3Console\Database\Configuration\ConnectionConfiguration;
use Helhum\Typo3Console\Database\Process\MysqlCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Dirk Wenzel <wenzel@cps-it.de>
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
trait ExecuteSqlTrait
{
    use InitializeTrait;

    protected ConnectionConfiguration $connectionConfiguration;

    /**
     * @var string
     */
    protected string $sqlToExecute = '';

    /**
     * SyncInstitutionPlaceFlatCommand constructor.
     * @param string|null $name
     * @param ConnectionConfiguration|null $connectionConfiguration
     * @param SymfonyStyle|null $io
     */
    public function __construct(
        string $name = null,
        ConnectionConfiguration $connectionConfiguration = null,
        SymfonyStyle $io = null
    ) {
        $this->sqlToExecute = file_get_contents(
            GeneralUtility::getFileAbsFileName(self::SQL_FILE_PATH)
        );
        $this->connectionConfiguration = $connectionConfiguration ?? GeneralUtility::makeInstance(
            ConnectionConfiguration::class
        );
        $this->io = $io;
        parent::__construct($name);
    }

    /**
     * Execute task
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = (string)$input->getOption(self::OPTION_CONNECTION);

        $this->io->comment(self::MESSAGE_STARTING);
        $availableConnections = $this->connectionConfiguration->getAvailableConnectionNames(self::CONNECTION_TYPE_MYSQL);

        if (empty($availableConnections) || !in_array($connection, $availableConnections, true)) {
            $this->io->error(self::ERROR_MISSING_CONNECTION);
            return 1_641_390_076;
        }
        $dbConfig = $this->connectionConfiguration->build($connection);

        if (!$output instanceof ConsoleOutput) {
            $this->io->error('Invalid output type. Please use ConsoleOutput.');
            return 1_641_390_077;
        }
        // this is clumsy: MysqlCommand only allows configuration as constructor argument.
        /** @noinspection PhpParamsInspection */
        $mysqlCommand = new MysqlCommand($dbConfig, $output);

        $inputStream = fopen('php://temp', 'r+');
        fwrite($inputStream, $this->sqlToExecute);
        rewind($inputStream);

        $exitCode = $mysqlCommand->mysql(
            self::DEFAULT_MYSQL_ARGUMENTS,
            $inputStream
        );

        if ($exitCode) {
            $this->io->error(self::ERROR_SQL_EXECUTION_FAILED);
            return 1_641_390_086;
        }

        $this->io->success(self::MESSAGE_SUCCESS);
        return 0;
    }
}
