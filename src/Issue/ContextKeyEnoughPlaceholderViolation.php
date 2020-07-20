<?php


namespace Sfp\Psalm\PsrLogPlugin\Issue;

use Psalm\CodeLocation;
use Psalm\Issue\PluginIssue;

class ContextKeyEnoughPlaceholderViolation extends PluginIssue
{
    public function __construct(string $message, array $context, CodeLocation $code_location)
    {
        $message = sprintf('Provided context keys are %', implode(',', array_keys($context)), $message);
        parent::__construct($message, $code_location);
    }
}