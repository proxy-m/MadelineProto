<?php

namespace Amp;

class InvalidYieldError extends \Error
{
    /**
     * @param \Generator $generator
     * @param string $prefix
     * @param (\Throwable | null) $previous
     */
    public function __construct($generator, $prefix, $previous = NULL)
    {
        if (!$generator instanceof \Generator) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($generator) must be of type Generator, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($generator) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!\is_string($prefix)) {
            if (!(\is_string($prefix) || \is_object($prefix) && \method_exists($prefix, '__toString') || (\is_bool($prefix) || \is_numeric($prefix)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($prefix) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($prefix) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $prefix = (string) $prefix;
            }
        }
        if (!($previous instanceof \Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #3 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        $yielded = $generator->current();
        $prefix .= \sprintf("; %s yielded at key %s", \is_object($yielded) ? \get_class($yielded) : \gettype($yielded), \var_export($generator->key(), true));
        if (!$generator->valid()) {
            parent::__construct($prefix, 0, $previous);
            return;
        }
        $reflGen = new \ReflectionGenerator($generator);
        $exeGen = $reflGen->getExecutingGenerator();
        if ($isSubgenerator = $exeGen !== $generator) {
            $reflGen = new \ReflectionGenerator($exeGen);
        }
        parent::__construct(\sprintf("%s on line %s in %s", $prefix, $reflGen->getExecutingLine(), $reflGen->getExecutingFile()), 0, $previous);
    }
}