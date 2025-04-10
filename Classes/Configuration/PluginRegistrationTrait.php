<?php

namespace DWenzel\T3extensionTools\Configuration;

trait PluginRegistrationTrait
{
    public function getPluginTitle(): string
    {
        return $this->pluginTitle;
    }

    public function getPluginDescription(): string
    {
        return $this->pluginDescription;
    }

    public function getPluginIcon(): string
    {
        return $this->pluginIcon;
    }

    public function getPluginGroup(): string
    {
        return $this->pluginGroup;
    }

    public function getFlexForm(): string
    {
        return $this->flexForm;
    }
}
