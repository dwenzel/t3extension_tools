<?php

namespace DWenzel\T3extensionTools\Command;

use DateInterval;
use DateTimeImmutable;
use DirectoryIterator;
use DWenzel\T3extensionTools\Command\Argument\AgeArgument;
use DWenzel\T3extensionTools\Command\Argument\DirectoryArgument;
use DWenzel\T3extensionTools\Command\Argument\FilePatternArgument;
use DWenzel\T3extensionTools\Traits\Command\ArgumentAwareTrait;
use DWenzel\T3extensionTools\Traits\Command\ConfigureTrait;
use DWenzel\T3extensionTools\Traits\Command\InitializeTrait;
use RegexIterator;
use SplFileInfo;
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

    public const DEFAULT_NAME = 't3extension-tools:delete-logs:delete-logs';
    public const MESSAGE_DESCRIPTION_COMMAND = 'Deletes a file according to retention policy.';
    public const MESSAGE_HELP_COMMAND = 'Delete log file';
    public const MESSAGE_SUCCESS = '%s file(s) successfully deleted';
    public const MESSAGE_STARTING = 'Start to delete log files';
    public const WARNING_MISSING_PARAMETER = 'Parameter "%s" must not be omitted';

    protected const ARGUMENTS = [
        AgeArgument::class,
        DirectoryArgument::class,
        FilePatternArgument::class,
    ];
    protected static $argumentsToConfigure = self::ARGUMENTS;

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePattern = $input->getArgument(FilePatternArgument::NAME);
        $maxAge = (int)$input->getArgument(AgeArgument::NAME);

        // [0-9]{4}-[0-9]{2}-[0-9]{2}_requests\.log
        $this->io->comment(static::MESSAGE_STARTING);

        $absDirectoryPath = $this->determineAbsoluteDirectoryPath($input);
        if (
            !(GeneralUtility::isAllowedAbsPath($absDirectoryPath)
                || PathUtility::isAllowedAdditionalPath($absDirectoryPath))
        ) {
            $this->io->error(
                sprintf('Invalid log file path %s', $absDirectoryPath)
            );

            return 1;
        }

        $currentDate = new DateTimeImmutable('today');
        $keepUntilDate = $currentDate->sub(new DateInterval("P{$input->getArgument(AgeArgument::name())}D"));

        $directoryIterator = new DirectoryIterator($absDirectoryPath);
        $fileList = new RegexIterator($directoryIterator, $filePattern);
        $deletedFilesCount = 0;
        foreach ($fileList as $file) {
            if (!$file instanceof SplFileInfo) {
                continue;
            }
            $age = 0;
            if ($this->hasDatePrefix($filePattern)) {
                // file pattern starts with expected date format (yyyy-mm-dd)
                $dateString = substr($file->getFilename(), 0, 10);
                $dateByPrefix = new DateTimeImmutable($dateString);
                $age = $currentDate->diff($dateByPrefix)->d;
            }

            // note: Most Unix OS do not record a files creation time
            // CTime reflects the time of last metadata change (e.g. access rights)
            if (!$this->hasDatePrefix($filePattern)
            ) {
                $changeDate = new DateTimeImmutable('@' . $file->getCTime());
                if ($changeDate < $keepUntilDate) {
                    continue;
                }

                $age = $currentDate->diff($changeDate)->d;
            }

            if ($age > $maxAge) {
                $filePath = $file->getRealPath();
                try {
                    unlink($filePath);
                    $deletedFilesCount++;
                } catch (\Exception $exception) {
                    $this->io->error($exception->getMessage());
                    return self::FAILURE;
                }
            }
        }

        $this->io->info(sprintf(self::MESSAGE_SUCCESS, $deletedFilesCount));

        return self::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function determineAbsoluteDirectoryPath(InputInterface $input): string
    {
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
     * @param mixed $filePattern
     * @return bool
     */
    protected function hasDatePrefix(mixed $filePattern): bool
    {
        return !empty($filePattern)
            && str_contains(
                haystack: $filePattern,
                needle: '/^[0-9]{4}-[0-9]{2}-[0-9]{2}_',
            );
    }
}
