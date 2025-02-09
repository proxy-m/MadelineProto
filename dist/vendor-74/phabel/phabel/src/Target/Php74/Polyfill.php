<?php

namespace Phabel\Target\Php74;

use Phabel\Plugin;
use Phabel\Target\Polyfill as TargetPolyfill;
/**
 * @author Daniil Gentili <daniil@daniil.it>
 * @license MIT
 */
class Polyfill extends Plugin
{
    // Todo: gmmktime, mktime
    /**
     *
     */
    public static function array_filter(array $array, ?callable $callback = NULL, int $mode = 0) : array
    {
        $callback ??= fn($v) => $v;
        return \array_filter($array, $callback, $mode);
    }
    /**
     * @param mixed $replacement
     */
    public static function array_splice(array &$array, int $offset, ?int $length = NULL, $replacement = array()) : array
    {
        if (!\true) {
            throw new \TypeError(__METHOD__ . '(): Argument #4 ($replacement) must be of type mixed, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($replacement) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        $length ??= \max(\count($array) - $offset, 0);
        return \array_splice($array, $offset, $length, $replacement);
    }
    /**
     * {@inheritDoc}
     */
    public static function withNext(array $config) : array
    {
        return [TargetPolyfill::class => [self::class => \true]];
    }
}