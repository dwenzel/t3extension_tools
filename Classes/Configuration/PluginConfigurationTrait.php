<?php

namespace DWenzel\T3extensionTools\Configuration;

trait PluginConfigurationTrait
{
    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getPluginType(): string
    {
        return $this->pluginType;
    }

    public function getNonCacheableControllerActions(): array
    {
        return $this->nonCacheableControllerActions;
    }

    public function getControllerActions(): array
    {
        return $this->controllerActions;
    }
}
