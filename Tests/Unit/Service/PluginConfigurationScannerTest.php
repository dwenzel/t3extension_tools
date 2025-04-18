<?php

namespace DWenzel\T3extensionTools\Tests\Unit\Service;

use DWenzel\T3extensionTools\Service\PluginConfigurationScanner;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Package\Package;
use TYPO3\CMS\Core\Package\PackageManager;

class PluginConfigurationScannerTest extends TestCase
{
    /**
     * @var PackageManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $packageManagerMock;

    /**
     * @var PluginConfigurationScanner
     */
    protected $subject;

    protected function setUp(): void
    {
        // Create mock for PackageManager
        $this->packageManagerMock = $this->createMock(PackageManager::class);

        // Create subject with mocked dependencies
        $this->subject = new PluginConfigurationScanner($this->packageManagerMock);
    }

    /**
     * @test
     */
    public function findExtensionsWithPluginConfigurationsReturnsCorrectExtensions(): void
    {
        // Create mock packages
        $package1 = $this->createMock(Package::class);
        $package1->method('getPackageKey')->willReturn('ext1');
        // Mock a path that would contain plugin configuration files
        $package1->method('getPackagePath')->willReturn('/virtual/ext1/');

        $package2 = $this->createMock(Package::class);
        $package2->method('getPackageKey')->willReturn('ext2');
        $package2->method('getPackagePath')->willReturn('/virtual/ext2/');

        $package3 = $this->createMock(Package::class);
        $package3->method('getPackageKey')->willReturn('ext3');
        $package3->method('getPackagePath')->willReturn('/virtual/ext3/');

        $package4 = $this->createMock(Package::class);
        $package4->method('getPackageKey')->willReturn('ext4');
        $package4->method('getPackagePath')->willReturn('/virtual/ext4/');

        // Setup PackageManager mock
        $this->packageManagerMock->method('getActivePackages')->willReturn([
            'ext1' => $package1,
            'ext2' => $package2,
            'ext3' => $package3,
            'ext4' => $package4
        ]);

        // Mock the Finder
        $finderMock = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->disableOriginalConstructor()
            ->getMock();

        // Mock a SplFileInfo for Plugin1.yaml
        $file1Mock = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
        $file1Mock->method('getRelativePathname')->willReturn('Plugin1.yaml');

        // Mock a SplFileInfo for Plugin2.yaml
        $file2Mock = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
        $file2Mock->method('getRelativePathname')->willReturn('Plugin2.yaml');

        // Mock a SplFileInfo for Plugin3.yaml in ext3
        $file3Mock = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
        $file3Mock->method('getRelativePathname')->willReturn('Plugin3.yaml');

        // Configure the Finder mock for ext1 to return two plugin files
        $finderMock1 = clone $finderMock;
        $finderMock1->method('files')->willReturnSelf();
        $finderMock1->method('in')->willReturnSelf();
        $finderMock1->method('name')->willReturnSelf();
        $finderMock1->method('hasResults')->willReturn(true);
        $finderMock1->method('getIterator')->willReturn(new \ArrayIterator([$file1Mock, $file2Mock]));

        // Configure the Finder mock for ext3 to return one plugin file
        $finderMock3 = clone $finderMock;
        $finderMock3->method('files')->willReturnSelf();
        $finderMock3->method('in')->willReturnSelf();
        $finderMock3->method('name')->willReturnSelf();
        $finderMock3->method('hasResults')->willReturn(true);
        $finderMock3->method('getIterator')->willReturn(new \ArrayIterator([$file3Mock]));

        // Configure the Finder mock for ext2 and ext4 to return no results
        $finderMock2 = clone $finderMock;
        $finderMock2->method('files')->willReturnSelf();
        $finderMock2->method('in')->willReturnSelf();
        $finderMock2->method('name')->willReturnSelf();
        $finderMock2->method('hasResults')->willReturn(false);

        $finderMock4 = clone $finderMock;
        $finderMock4->method('files')->willReturnSelf();
        $finderMock4->method('in')->willReturnSelf();
        $finderMock4->method('name')->willReturnSelf();
        $finderMock4->method('hasResults')->willReturn(false);

        // Create a partial mock of the scanner to handle directory existence and Finder creation
        $subjectMock = $this->getMockBuilder(PluginConfigurationScanner::class)
            ->setConstructorArgs([$this->packageManagerMock])
            ->onlyMethods(['createFinder', 'isPluginDirectory'])
            ->getMock();

        // Configure isPluginDirectory mock
        $subjectMock->method('isPluginDirectory')
            ->willReturnMap([
                ['/virtual/ext1/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, true],
                ['/virtual/ext2/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, false],
                ['/virtual/ext3/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, true],
                ['/virtual/ext4/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, false],
            ]);

        // Configure createFinder mock
        $subjectMock->method('createFinder')
            ->willReturnMap([
                ['/virtual/ext1/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, $finderMock1],
                ['/virtual/ext2/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, $finderMock2],
                ['/virtual/ext3/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, $finderMock3],
                ['/virtual/ext4/' . PluginConfigurationScanner::PLUGINS_DIRECTORY, $finderMock4],
            ]);

        // Call method and check result
        $result = $subjectMock->findExtensionsWithPluginConfigurations();

        // ext1 and ext3 should have plugin configurations
        $this->assertArrayHasKey('ext1', $result);
        $this->assertArrayHasKey('ext3', $result);
        $this->assertCount(2, $result);

        // Check that plugin files were correctly detected
        $this->assertContains('Plugin1.yaml', $result['ext1']);
        $this->assertContains('Plugin2.yaml', $result['ext1']);
        $this->assertContains('Plugin3.yaml', $result['ext3']);

    }

    /**
     * @test
     */
    public function getPluginConfigurationPathReturnsCorrectPath(): void
    {
        $extensionKey = 'ext1';
        $fileName = 'Plugin1.yaml';
        $expectedPath = '/virtual/ext1/Configuration/Plugins/Plugin1.yaml';

        // Create mock package
        $package = $this->createMock(Package::class);
        $package->method('getPackagePath')->willReturn('/virtual/ext1/');

        // Setup PackageManager mock
        $this->packageManagerMock->method('getPackage')
            ->with($extensionKey)
            ->willReturn($package);

        // Call method and check result
        $result = $this->subject->getPluginConfigurationPath($extensionKey, $fileName);
        $this->assertEquals($expectedPath, $result);
    }
}
