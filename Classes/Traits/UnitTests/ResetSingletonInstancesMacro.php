<?php
/**
 * This file is part of the iki Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * README.md file that was distributed with this source code.
 */

namespace DWenzel\T3extensionTools\Traits\UnitTests;

trait ResetSingletonInstancesMacro
{
    public function setResetSingletonInstances(): void
    {
        $this->resetSingletonInstances = true;
    }
}
