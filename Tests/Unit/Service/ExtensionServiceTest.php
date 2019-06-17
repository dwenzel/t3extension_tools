<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3extensionTools\Service\ExtensionService;
use DWenzel\T3extensionTools\Configuration\SettingsInterface as SI;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class ExtensionServiceTest
 */
class ExtensionServiceTest extends UnitTestCase
{
    /**
     * @var ExtensionService|MockObject
     */
    protected $subject;

    /**
     * set up
     */
    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            ExtensionService::class, ['dummy']
        );
    }

    /**
     * @test
     */
    public function isActionCacheableInitiallyReturnsTrue()
    {
        $configuration = [];
        $extensionName = 'foo';
        $pluginName = 'bar';
        $controllerName = 'baz';
        $actionName = 'bam';

        /** @var ConfigurationManagerInterface|MockObject $mockConfigurationManager */
        $mockConfigurationManager = $this->getMockForAbstractClass(
            ConfigurationManagerInterface::class
        );

        $this->subject->injectConfigurationManager($mockConfigurationManager);

        $mockConfigurationManager->method('getConfiguration')
            ->willReturn($configuration);

        $this->assertTrue(
            $this->subject->isActionCacheable($extensionName, $pluginName, $controllerName, $actionName)
        );
    }

    /**
     * @test
     */
    public function isActionCacheableReturnsIfIsSetForAction()
    {
        $configuration = [
            SI::SETTINGS => [
                SI::CACHE => [
                    SI::NOT_CACHEABLE => '1'
                ]
            ]
        ];
        $extensionName = 'foo';
        $pluginName = 'bar';
        $controllerName = 'baz';
        $actionName = 'bam';

        /** @var ConfigurationManagerInterface|MockObject $mockConfigurationManager */
        $mockConfigurationManager = $this->getMockForAbstractClass(
            ConfigurationManagerInterface::class
        );

        $this->subject->injectConfigurationManager($mockConfigurationManager);

        $mockConfigurationManager->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, $extensionName, $pluginName)
            ->willReturn($configuration);

        $this->assertFalse(
            $this->subject->isActionCacheable($extensionName, $pluginName, $controllerName, $actionName)
        );
    }
}
