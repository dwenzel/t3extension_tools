<?php

namespace DWenzel\T3extensionTools\Traits\Command;

use Doctrine\DBAL\Exception as DbalException;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
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
 * The file is read when the command runs. A missing or unreadable file ends
 * the command with an error. The statements are executed one by one through
 * Doctrine DBAL; no database client binary is needed. Execution stops at the
 * first failing statement; statements executed before it are not rolled back.
 *
 * Supported SQL (see splitSqlStatements()):
 * - statements are separated by ';' outside of quotes and comments, also
 *   several statements on one line
 * - '...' and "..." (doubled quotes; backslash escapes on MySQL/MariaDB
 *   connections) and `...` are skipped when looking for ';'
 * - comments: '#' and '-- ' up to the end of the line, '/* ... *\/' anywhere;
 *   MySQL executable comments and optimizer hints ('/*! ... *\/',
 *   '/*+ ... *\/') are kept
 * - not supported: client commands of the mysql CLI (DELIMITER, SOURCE, \G,
 *   ...), therefore no procedures or triggers with custom delimiters, and
 *   PostgreSQL dollar-quoted strings ($$ ... $$)
 */
trait ExecuteSqlTrait
{
    use InitializeTrait;

    /**
     * Content of the SQL file. Filled in execute(); a value assigned earlier
     * is overwritten.
     */
    protected string $sqlToExecute = '';

    public function __construct(
        ?string $name = null
    ) {
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

        $sql = $this->readSqlFile($this->getSqlFilePath());
        if ($sql === null) {
            $this->io->error(sprintf(
                'The SQL file "%s" does not exist or is not readable.',
                $this->getSqlFilePath()
            ));
            return 1_795_339_730;
        }
        $this->sqlToExecute = $sql;

        try {
            $connection = $connectionPool->getConnectionByName($connectionName);
            $backslashEscapes = $connection->getDatabasePlatform() instanceof AbstractMySQLPlatform;
            foreach ($this->splitSqlStatements($this->sqlToExecute, $backslashEscapes) as $statement) {
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
     * Path of the SQL file, absolute or 'EXT:' notation.
     */
    protected function getSqlFilePath(): string
    {
        return static::SQL_FILE_PATH;
    }

    /**
     * Returns the content of the file or null if it is missing or not readable.
     */
    protected function readSqlFile(string $path): ?string
    {
        $absolutePath = $path === '' ? '' : GeneralUtility::getFileAbsFileName($path);
        if ($absolutePath === '' || !is_file($absolutePath) || !is_readable($absolutePath)) {
            return null;
        }
        $content = @file_get_contents($absolutePath);

        return $content === false ? null : $content;
    }

    /**
     * Splits SQL into single statements.
     *
     * A statement ends with ';' outside of quotes and comments. Quoted strings
     * and identifiers ('...', "...", `...`) are copied unchanged; a quote is
     * escaped by doubling it and, if $backslashEscapes is true (MySQL/MariaDB),
     * by a backslash inside '...' and "...". Comments ('#' and '-- ' up to the
     * end of the line, '/* ... *\/') are removed, except MySQL executable
     * comments and optimizer hints ('/*! ... *\/', '/*+ ... *\/'). Empty
     * statements are skipped.
     *
     * @return list<string>
     */
    protected function splitSqlStatements(string $sql, bool $backslashEscapes = true): array
    {
        $statements = [];
        $current = '';
        $length = strlen($sql);
        $position = 0;
        while ($position < $length) {
            $plainLength = strcspn($sql, ";'\"`#-/", $position);
            $current .= substr($sql, $position, $plainLength);
            $position += $plainLength;
            if ($position >= $length) {
                break;
            }

            $char = $sql[$position];
            $next = $sql[$position + 1] ?? '';
            if ($char === ';') {
                $statements = $this->addSqlStatement($statements, $current);
                $current = '';
                $position++;
            } elseif ($char === "'" || $char === '"' || $char === '`') {
                $end = $this->findEndOfQuotedSql($sql, $position, $backslashEscapes && $char !== '`');
                $current .= substr($sql, $position, $end - $position);
                $position = $end;
            } elseif ($char === '#' || ($char === '-' && $next === '-' && $this->isSqlWhitespaceOrEnd($sql[$position + 2] ?? ''))) {
                // line comment: keep the line break
                $endOfLine = strpos($sql, "\n", $position);
                $position = $endOfLine === false ? $length : $endOfLine;
            } elseif ($char === '/' && $next === '*') {
                $endOfComment = strpos($sql, '*/', $position + 2);
                $end = $endOfComment === false ? $length : $endOfComment + 2;
                $marker = $sql[$position + 2] ?? '';
                $current .= ($marker === '!' || $marker === '+') ? substr($sql, $position, $end - $position) : ' ';
                $position = $end;
            } else {
                $current .= $char;
                $position++;
            }
        }

        return $this->addSqlStatement($statements, $current);
    }

    /**
     * @param list<string> $statements
     * @return list<string>
     */
    private function addSqlStatement(array $statements, string $statement): array
    {
        $statement = trim($statement);
        if ($statement !== '') {
            $statements[] = $statement;
        }

        return $statements;
    }

    /**
     * Returns the position after the closing quote, or the length of $sql if
     * the quote is not closed.
     */
    private function findEndOfQuotedSql(string $sql, int $start, bool $backslashEscapes): int
    {
        $quote = $sql[$start];
        $length = strlen($sql);
        $position = $start + 1;
        while ($position < $length) {
            $char = $sql[$position];
            if ($backslashEscapes && $char === '\\') {
                $position += 2;
                continue;
            }
            if ($char === $quote) {
                if (($sql[$position + 1] ?? '') !== $quote) {
                    return $position + 1;
                }
                $position++;
            }
            $position++;
        }

        return $length;
    }

    private function isSqlWhitespaceOrEnd(string $char): bool
    {
        return $char === '' || ctype_space($char) || ctype_cntrl($char);
    }
}
