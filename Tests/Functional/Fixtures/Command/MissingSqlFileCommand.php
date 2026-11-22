<?php

declare(strict_types=1);

/*
 * This file is part of the "t3extension_tools" extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version (GPL-2.0-or-later).
 */

namespace DWenzel\T3extensionTools\Tests\Functional\Fixtures\Command;

use DWenzel\T3extensionTools\Command\Option\ConnectionOption;
use DWenzel\T3extensionTools\Command\OptionAwareInterface;
use DWenzel\T3extensionTools\Traits\Command\ConfigureTrait;
use DWenzel\T3extensionTools\Traits\Command\ExecuteSqlTrait;
use DWenzel\T3extensionTools\Traits\Command\OptionAwareTrait;
use Symfony\Component\Console\Command\Command;

/**
 * Uses ExecuteSqlTrait directly, with an SQL_FILE_PATH that does not exist.
 */
final class MissingSqlFileCommand extends Command implements OptionAwareInterface
{
    use ConfigureTrait;
    use OptionAwareTrait;
    use ExecuteSqlTrait;

    public const string SQL_FILE_PATH = 'EXT:t3extension_tools/Tests/Functional/Fixtures/Sql/does-not-exist.sql';
    public const string MESSAGE_DESCRIPTION_COMMAND = 'Fixture command';
    public const string MESSAGE_HELP_COMMAND = 'Fixture command with a missing SQL file';
    public const string MESSAGE_SUCCESS = 'done';
    public const string MESSAGE_STARTING = 'Starting...';
    public const string OPTION_CONNECTION = ConnectionOption::NAME;
    public const string ERROR_MISSING_CONNECTION = 'No database connection with this name found.';
    public const string ERROR_SQL_EXECUTION_FAILED = 'Execution of SQL statement failed';

    /**
     * @var array|string[]
     */
    protected static array $optionsToConfigure = [
        ConnectionOption::class,
    ];
}
