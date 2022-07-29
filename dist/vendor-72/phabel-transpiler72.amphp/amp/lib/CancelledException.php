<?php

namespace Amp;

/**
 * Will be thrown in case an operation is cancelled.
 *
 * @see CancellationToken
 * @see CancellationTokenSource
 */
class CancelledException extends \Exception
{
    /**
     *
     * @param (\Throwable | null) $previous
     */
    public function __construct($previous = NULL)
    {
        if (!($previous instanceof \Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct("The operation was cancelled", 0, $previous);
    }
}