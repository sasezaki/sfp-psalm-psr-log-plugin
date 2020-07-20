<?php

namespace Sfp\Psalm\PsrLogPlugin;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

class Plugin implements PluginEntryPointInterface
{
    /** @return void */
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null)
    {
        require_once __DIR__ . '/LoggerHandler.php';

        $psalm->registerHooksFromClass(LoggerHandler::class);

        if (isset($config->throwable) && ((bool)$config->throwable === true)) {
            $psalm->addStubFile(__DIR__ . '/../throwable-stubs/LoggerInterface.php');
            $psalm->addStubFile(__DIR__ . '/../throwable-stubs/AbstractLogger.php');
        } else {
            $psalm->addStubFile(__DIR__ . '/../stubs/LoggerInterface.php');
            $psalm->addStubFile(__DIR__ . '/../stubs/AbstractLogger.php');
        }
    }
}
