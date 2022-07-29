<?php

namespace Phabel\PluginGraph;

use Phabel\PluginInterface;
/**
 * Circular reference in plugin graph.
 *
 * @author Daniil Gentili <daniil@daniil.it>
 * @license MIT
 */
class CircularException extends \Exception
{
    /**
     * Plugin array.
     *
     * @var class-string<PluginInterface>[]
     */
    private $plugins = [];
    /**
     * Constructor.
     *
     * @param class-string<PluginInterface>[] $plugins Plugin array
     * @param \Throwable $previous Previous exception
     */
    public function __construct($plugins, $previous = NULL)
    {
        if (!\is_array($plugins)) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($plugins) must be of type array, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($plugins) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!($previous instanceof \Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        $this->plugins = $plugins;
        parent::__construct("Detected circular reference: " . \implode(" => ", $plugins), 0, $previous);
    }
    /**
     * Get plugins.
     *
     * @return class-string<PluginInterface>[]
     */
    public function getPlugins() : array
    {
        return $this->plugins;
    }
}