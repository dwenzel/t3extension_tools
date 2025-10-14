<?php

namespace DWenzel\T3extensionTools\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2025 Dirk Wenzel <wenzel@cps-it.de>
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

use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * Class PluginConfigurationScanner
 *
 * This class scans all TYPO3 extensions for plugin configuration files.
 */
class PluginConfigurationScanner
{
    public const string PLUGINS_DIRECTORY = 'Configuration/Plugins';

    public function __construct(
        protected PackageManager $packageManager
    ) {}

    /**
     * Finds all extensions with plugin configuration files in YAML format
     *
     * @return array Array of extension keys with their plugin configuration files
     */
    //...
    public function findExtensionsWithPluginConfigurations(): array
    {
        $extensionsWithPlugins = [];
        $activePackages = $this->packageManager->getActivePackages();
        foreach ($activePackages as $package) {
            $extensionKey = $package->getPackageKey();
            $pluginsPath = $package->getPackagePath() . self::PLUGINS_DIRECTORY;
            if ($this->isPluginDirectory($pluginsPath)) {
                $extensionsWithPlugins[$extensionKey] = $this->getPluginFiles($pluginsPath);
            }
        }
        return $extensionsWithPlugins;
    }

    private function getPluginFiles(string $pluginsPath): array
    {
        $finder = $this->createFinder($pluginsPath);
        if (!$finder->hasResults()) {
            return [];
        }
        $pluginFiles = [];
        foreach ($finder as $file) {
            $pluginFiles[] = $file->getRelativePathname();
        }
        return $pluginFiles;
    }
    //...

    /**
     * Get the absolute path to a plugin configuration file
     *
     * @param string $extensionKey The extension key
     * @param string $fileName The plugin configuration file name
     * @return string The absolute path to the plugin configuration file
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    public function getPluginConfigurationPath(string $extensionKey, string $fileName): string
    {
        $package = $this->packageManager->getPackage($extensionKey);
        return $package->getPackagePath() . self::PLUGINS_DIRECTORY . '/' . $fileName;
    }

    /**
     * Check if the given path is a valid plugin directory
     *
     * @param string $path The path to check
     * @return bool Whether the path exists and is a directory
     */
    protected function isPluginDirectory(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Create a Finder instance for a given path
     *
     * @param string $path The path to search in
     * @return \Symfony\Component\Finder\Finder
     */
    protected function createFinder(string $path): \Symfony\Component\Finder\Finder
    {
        $finder = new Finder();
        return $finder->files()->in($path)->name('*.yaml');
    }
}
