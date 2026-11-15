<?php

namespace DWenzel\T3extensionTools\Command;

use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Traits\Command\ArgumentAwareTrait;
use DWenzel\T3extensionTools\Traits\Command\ConfigureTrait;
use DWenzel\T3extensionTools\Traits\Command\InitializeTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2024 Dirk Wenzel <wenzel@cps-it.de>
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
class DeleteLogs extends Command implements ArgumentAwareInterface
{
    use ArgumentAwareTrait;
    use ConfigureTrait;
    use InitializeTrait;

    public const DEFAULT_NAME = 't3extension-tools:delete-logs';
    public const MESSAGE_DESCRIPTION_COMMAND = 'Deletes a file according to retention policy.';
    public const MESSAGE_HELP_COMMAND = 'Delete log file';
    public const MESSAGE_SUCCESS = '%s file(s) successfully deleted';
    public const MESSAGE_STARTING = 'Start to delete log files';
    public const WARNING_MISSING_PARAMETER = 'Parameter "%s" must not be omitted';
    public const ERROR_INVALID_AGE = 'Invalid age "%s". The age must be a number of days (0 or more).';
    public const ERROR_INVALID_PATTERN = 'Invalid file pattern "%s". The pattern must be a regular expression including delimiters.';
    public const ERROR_INVALID_PATH = 'Invalid log file path %s';
    public const ERROR_DELETE_FAILED = 'Could not delete file %s';

    protected const DATE_PREFIX_PATTERN = '^[0-9]{4}-[0-9]{2}-[0-9]{2}';
    protected const DATE_FORMAT = 'Y-m-d';
    protected const SECONDS_PER_DAY = 86400;

    protected const ARGUMENTS = [
        AgeArgument::class,
        DirectoryArgument::class,
        FilePatternArgument::class,
    ];
    /**
     * @var array|string[] $argumentsToConfigure
     * @noinspection PhpMissingFieldTypeInspection
     */
    protected static array $argumentsToConfigure = self::ARGUMENTS;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePattern = (string)$input->getArgument(FilePatternArgument::NAME);
        $maxAge = (string)$input->getArgument(AgeArgument::NAME);

        if ($filePattern === '') {
            $this->io->error(sprintf(self::WARNING_MISSING_PARAMETER, FilePatternArgument::NAME));
            return self::INVALID;
        }
        // preg_match() returns false and raises a warning for an invalid pattern
        if (@preg_match($filePattern, '') === false) {
            $this->io->error(sprintf(self::ERROR_INVALID_PATTERN, $filePattern));
            return self::INVALID;
        }
        if (!ctype_digit($maxAge)) {
            $this->io->error(sprintf(self::ERROR_INVALID_AGE, $maxAge));
            return self::INVALID;
        }

        $this->io->comment(static::MESSAGE_STARTING);

        $absDirectoryPath = $this->determineAbsoluteDirectoryPath($input);
        if (
            !(GeneralUtility::isAllowedAbsPath($absDirectoryPath)
                || PathUtility::isAllowedAdditionalPath($absDirectoryPath))
            || !is_dir($absDirectoryPath)
        ) {
            $this->io->error(sprintf(self::ERROR_INVALID_PATH, $absDirectoryPath));
            return self::FAILURE;
        }

        $useDatePrefix = $this->hasDatePrefix($filePattern);
        $deletedFilesCount = 0;
        $failedFilesCount = 0;
        foreach (new \RegexIterator(new \DirectoryIterator($absDirectoryPath), $filePattern) as $file) {
            if (!$file instanceof \SplFileInfo || !$file->isFile()) {
                continue;
            }
            if ($this->determineAgeInDays($file, $useDatePrefix) <= (int)$maxAge) {
                continue;
            }
            // unlink() raises a warning and returns false on failure
            if (!@unlink($file->getPathname())) {
                $this->io->error(sprintf(self::ERROR_DELETE_FAILED, $file->getPathname()));
                $failedFilesCount++;
                continue;
            }
            $deletedFilesCount++;
        }

        $this->io->info(sprintf(self::MESSAGE_SUCCESS, $deletedFilesCount));

        return $failedFilesCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Age in full days. Uses the date at the beginning of the file name
     * (yyyy-mm-dd) if the pattern expects one and the file name starts
     * with a valid date, otherwise the modification time.
     */
    protected function determineAgeInDays(\SplFileInfo $file, bool $useDatePrefix): int
    {
        if ($useDatePrefix) {
            $prefix = substr($file->getFilename(), 0, 10);
            $date = \DateTimeImmutable::createFromFormat('!' . self::DATE_FORMAT, $prefix);
            if ($date instanceof \DateTimeImmutable && $date->format(self::DATE_FORMAT) === $prefix) {
                $today = new \DateTimeImmutable('today');
                return $date < $today ? (int)$today->diff($date)->days : 0;
            }
        }

        return (int)floor((time() - $file->getMTime()) / self::SECONDS_PER_DAY);
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function determineAbsoluteDirectoryPath(InputInterface $input): string
    {
        $absDirectoryPath = '';
        $directory = PathUtility::getCanonicalPath(
            (string)$input->getArgument(DirectoryArgument::NAME)
        );

        // default path is `var/log`
        if ($directory === DirectoryArgument::DEFAULT) {
            $absDirectoryPath = Environment::getVarPath() . '/log';
        }

        // allow only public path otherwise
        if ($directory !== DirectoryArgument::DEFAULT) {
            $absDirectoryPath = Environment::getPublicPath() . '/' . PathUtility::getCanonicalPath($directory);
        }

        if (PathUtility::isAbsolutePath($directory)) {
            $absDirectoryPath = $directory;
        }
        return $absDirectoryPath;
    }

    /**
     * Whether the pattern expects file names starting with a date (yyyy-mm-dd),
     * e.g. `/^[0-9]{4}-[0-9]{2}-[0-9]{2}_requests\.log/`. Any delimiter is accepted.
     */
    protected function hasDatePrefix(mixed $filePattern): bool
    {
        if (!is_string($filePattern) || strlen($filePattern) < 2) {
            return false;
        }

        return str_starts_with(substr($filePattern, 1), self::DATE_PREFIX_PATTERN);
    }
}
