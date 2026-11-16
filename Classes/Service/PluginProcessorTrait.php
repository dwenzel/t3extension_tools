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
trait PluginProcessorTrait
{
    /**
     * Calls the callback for each plugin implementing the interface.
     * Plugins are the objects collected by the service container
     * (tagged services). Class names are still accepted and instantiated
     * without constructor arguments.
     *
     * @param iterable<class-string|object> $plugins
     */
    private function processPlugins(iterable $plugins, callable $callback, string $interface): void
    {
        foreach ($plugins as $plugin) {
            if (is_string($plugin)) {
                if (!class_exists($plugin) || !is_subclass_of($plugin, $interface)) {
                    continue;
                }
                $plugin = new $plugin();
            }
            if ($plugin instanceof $interface) {
                $callback($plugin);
            }
        }
    }
}
