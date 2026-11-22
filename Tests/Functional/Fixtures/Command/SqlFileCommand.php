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

use DWenzel\T3extensionTools\Command\ExampleCommand;

/**
 * ExampleCommand with a configurable SQL file.
 */
final class SqlFileCommand extends ExampleCommand
{
    public function __construct(private readonly string $sqlFilePath)
    {
        parent::__construct();
    }

    protected function getSqlFilePath(): string
    {
        return $this->sqlFilePath;
    }
}
