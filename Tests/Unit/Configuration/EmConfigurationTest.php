<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Configuration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Dirk Wenzel <wenzel@cps-it.de>
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

use DWenzel\T3extensionTools\Configuration\EmConfiguration;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DWenzel\T3extensionTools\Configuration\SettingsInterface as SI;

class EmConfigurationTest extends UnitTestCase
{
    /**
     * @var EmConfiguration|MockObject
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = new  EmConfiguration();
    }

    public function testExtendedExtensionServiceEnabledInitiallyReturnsFalse()
    {
        $this->assertFalse(
            $this->subject->extensionServiceEnabled()
        );
    }

    public function testExtendedExtensionServiceCanBeEnabled()
    {
        $this->subject->enableExtensionService();
        $this->assertTrue(
            $this->subject->extensionServiceEnabled()
        );
    }

    public function testExtendedExtensionServiceCanBeDisabled()
    {
        $this->subject->enableExtensionService();
        $this->subject->disableExtensionService();
        $this->assertFalse(
            $this->subject->extensionServiceEnabled()
        );
    }

    public function testConstructorEnablesExtendedExtensionService()
    {
        $configuration = [
            'extensionServiceEnabled' => true
        ];
        $this->subject = new EmConfiguration($configuration);
        $this->assertTrue(
            $this->subject->extensionServiceEnabled()
        );
    }
}
