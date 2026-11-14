<?php

declare(strict_types=1);

use DWenzel\T3extensionTools\Command\ExampleCommand;
use Helhum\Typo3Console\Database\Configuration\ConnectionConfigurationFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/*
 * The example command uses ExecuteSqlTrait, which depends on
 * helhum/typo3-console. The console is optional (see "suggest" in
 * composer.json), so the command is only registered when it is installed.
 */
return static function (ContainerConfigurator $containerConfigurator): void {
    if (!class_exists(ConnectionConfigurationFactory::class)) {
        return;
    }

    $containerConfigurator->services()
        ->set(ExampleCommand::class)
        ->autowire()
        ->autoconfigure()
        ->tag('console.command', [
            'command' => 't3extension-tools:example',
            'description' => 'Download file to location',
            'schedulable' => false,
        ]);
};
