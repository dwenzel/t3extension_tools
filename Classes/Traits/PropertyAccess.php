<?php

namespace DWenzel\T3extensionTools\Traits;

use DWenzel\T3extensionTools\Configuration\InvalidConfigurationException;

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
trait PropertyAccess
{
    /**
     * @param string $propertyName
     * @return mixed
     * @throws InvalidConfigurationException
     */
    protected static function getStaticProperty(string $propertyName): mixed
    {
        if (property_exists(self::class, $propertyName)) {
            return static::$$propertyName;
        }
        throw new InvalidConfigurationException(
            "Missing property $propertyName in class" . self::class . '.',
            1_565_600_918
        );
    }
}
