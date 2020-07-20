<?php
namespace Sfp\Psalm\PsrLogPlugin;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\IssueBuffer;
use Psalm\Plugin\Hook\AfterMethodCallAnalysisInterface;
use Psalm\StatementsSource;
use Psalm\Type\Union;
use Psr\Log\LoggerInterface;
use Sfp\Psalm\PsrLogPlugin\Issue\ContextKeyEnoughPlaceholderViolation;

final class LoggerHandler implements AfterMethodCallAnalysisInterface
{
    private const PSR_LOG_LEVEL_METHODS = [
        'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'
    ];

    /**
     * {@inheritdoc}
     */
    public static function afterMethodCallAnalysis(
        Expr $expr,
        string $method_id,
        string $appearing_method_id,
        string $declaring_method_id,
        Context $context,
        StatementsSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = [],
        Union &$return_type_candidate = null
    ) {

        $method_id = new \Psalm\Internal\MethodIdentifier(...explode('::', $declaring_method_id));
        $function_storage = $codebase->methods->getStorage($method_id);
        $class_like_storage = $codebase->classlike_storage_provider->get($function_storage->defining_fqcln);

        if (!in_array(LoggerInterface::class, $class_like_storage->class_implements, true)) {
            return;
        }

        if ('log' === $method_id->method_name) {
            $message = null;
            $context = [];

            if ($expr->args[1]->value instanceof String_) {
                $message = $expr->args[1]->value->value;
            }

            // todo - message should not has empty - `{}` or context should not has empty key - `''`
            // todo validate brace is not nested.


        }

        if (in_array($method_id->method_name, self::PSR_LOG_LEVEL_METHODS, true)) {
            $message = null;
            if ($expr->args[0]->value instanceof String_) {
                $message = $expr->args[0]->value->value;
            }

            $context = [];
            if ($expr->args[1]->value instanceof Expr\Array_) {
                foreach ($expr->args[1]->value->items as $item) {
                    $context[$item->key->value] = $item->value->value;
                }
            }


            self::validatePlaceholderIsMatch($message, $context, $expr->args[0]->value, $statements_source);
        }
    }

    /**
     * $context - non string-castable vars would through
     */
    public static function validatePlaceholderIsMatch(string $message, array $context, $argument, StatementsSource $statements_source)
    {

        // todo
        // self::validatePlaceholderName($message);

        // todo
        // check non string-castable var is assigned

        if (self::contextKeyEnoughPlaceholder(
            self::extractPlaceholders($message),
            self::buildReplacements($context)
        )) {
            return;
        }

        IssueBuffer::accepts(
            new ContextKeyEnoughPlaceholderViolation($message, $context, new CodeLocation($statements_source, $argument)),
            $statements_source->getSuppressedIssues()
        );
    }

    public static function contextKeyEnoughPlaceholder(array $placeholders, array $replacementables) : bool
    {
        $replacementableKeys = array_keys($replacementables);
        $intersects = array_intersect($placeholders, $replacementableKeys);

        return count($placeholders) === count($intersects);
    }

    public static function extractPlaceholders(string $message) : array
    {
        $placeholders = [[]];
        preg_match_all('/\{[A-Z0-9a-z_.]+\}/', $message, $placeholders);

        return $placeholders[0];
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, string>
     */
    public static function buildReplacements(array $context) : array
    {
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = (string) $val;
            }
        }

        return $replace;
    }

    /**
     * @todo impl
     *
     * ```
     * There MUST NOT be any whitespace between the delimiters and the placeholder name.
     *
     * Placeholder names SHOULD be composed only of the characters A-Z, a-z, 0-9, underscore _, and period ..
     * The use of other characters is reserved for future modifications of the placeholders specification.
     * ```
     */
    public static function validatePlaceholderName(string $message)
    {

    }
}