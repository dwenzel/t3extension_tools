<?php

declare(strict_types=1);

/*
 * This file is part of the "t3extension_tools" extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version (GPL-2.0-or-later).
 */

namespace DWenzel\T3extensionTools\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ExtensionLoadedTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = ['t3extension_tools'];

    #[Test]
    public function extensionIsLoadedAndDatabaseIsAvailable(): void
    {
        self::assertTrue(ExtensionManagementUtility::isLoaded('t3extension_tools'));

        $count = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('pages')
            ->count('*', 'pages', []);

        self::assertSame(0, $count);
    }
}
