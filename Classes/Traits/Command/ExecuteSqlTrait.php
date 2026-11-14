<?php

namespace DWenzel\T3extensionTools\Traits\Command;

use Doctrine\DBAL\Exception as DbalException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
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

/**
 * Executes the SQL file SQL_FILE_PATH on a TYPO3 database connection.
 *
 * The statements are executed one by one through Doctrine DBAL. No database
 * client binary is needed. Format of the SQL file: every statement ends with
 * a semicolon at the end of a line; lines starting with '#' or '--' and
 * C-style comment blocks are skipped. Client commands like DELIMITER or
 * SOURCE are not supported. Execution stops at the first failing statement;
 * statements executed before it are not rolled back.
 */
trait ExecuteSqlTrait
{
    use InitializeTrait;

    protected string $sqlToExecute = '';

    public function __construct(
        ?string $name = null
    ) {
        $this->sqlToExecute = (string)file_get_contents(
            GeneralUtility::getFileAbsFileName(self::SQL_FILE_PATH)
        );
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connectionName = (string)$input->getOption(self::OPTION_CONNECTION);

        $this->io->comment(self::MESSAGE_STARTING);
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        if (!in_array($connectionName, $connectionPool->getConnectionNames(), true)) {
            $this->io->error(self::ERROR_MISSING_CONNECTION);
            return 1_641_390_076;
        }

        try {
            $connection = $connectionPool->getConnectionByName($connectionName);
            foreach ($this->splitSqlStatements($this->sqlToExecute) as $statement) {
                $connection->executeStatement($statement);
            }
        } catch (DbalException $exception) {
            $this->io->error([self::ERROR_SQL_EXECUTION_FAILED, $exception->getMessage()]);
            return 1_641_390_086;
        }

        $this->io->success(self::MESSAGE_SUCCESS);
        return 0;
    }

    /**
     * Splits SQL into single statements: a statement ends with a semicolon
     * at the end of a line. Comment lines are skipped.
     *
     * @return list<string>
     */
    protected function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $isInComment = false;
        foreach (preg_split('/\R/', $sql) ?: [] as $line) {
            $line = trim($line);
            if ($isInComment) {
                $isInComment = !str_ends_with($line, '*/');
                continue;
            }
            if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, '--')) {
                continue;
            }
            if (str_starts_with($line, '/*')) {
                $isInComment = !str_ends_with($line, '*/');
                continue;
            }
            $current .= ($current === '' ? '' : ' ') . $line;
            if (str_ends_with($line, ';')) {
                $statement = trim(rtrim($current, ';'));
                if ($statement !== '') {
                    $statements[] = $statement;
                }
                $current = '';
            }
        }
        $statement = trim($current);
        if ($statement !== '') {
            $statements[] = $statement;
        }

        return $statements;
    }
}
