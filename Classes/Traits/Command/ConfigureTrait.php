<?php

namespace DWenzel\T3extensionTools\Traits\Command;

use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use DWenzel\T3extensionTools\Command\ArgumentAwareInterface;
use DWenzel\T3extensionTools\Command\Option\InputOptionInterface;
use DWenzel\T3extensionTools\Command\OptionAwareInterface;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2021 Dirk Wenzel <wenzel@cps-it.de>
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
trait ConfigureTrait
{
    /**
     * Command configuration
     */
    protected function configure(): void
    {
        $this->setDescription(self::MESSAGE_DESCRIPTION_COMMAND);
        $this->setHelp(self::MESSAGE_HELP_COMMAND);

        if ($this instanceof ArgumentAwareInterface) {
            foreach ($this->getArguments() as $argument) {
                if (!in_array(InputArgumentInterface::class, class_implements($argument), true)) {
                    continue;
                }
                $this->addArgument(
                    $argument::name(),
                    $argument::mode(),
                    $argument::description(),
                    $argument::defaultValue()
                );
            }
        }
        if ($this instanceof OptionAwareInterface) {
            // @extensionScannerIgnoreLine
            foreach ($this->getOptions() as $option) {
                if (!in_array(InputOptionInterface::class, class_implements($option), true)) {
                    continue;
                }
                $this->addOption(
                    $option::name(),
                    $option::shortCut(),
                    $option::mode(),
                    $option::description(),
                    $option::defaultValue()
                );
            }
        }
    }

    abstract public function setDescription(string $description);

    abstract public function setHelp(string $help);

}
