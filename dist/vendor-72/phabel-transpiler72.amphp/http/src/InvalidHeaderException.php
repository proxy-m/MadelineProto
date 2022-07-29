<?php

namespace Amp\Http;

final class InvalidHeaderException extends \Exception
{
    /**
     * Thrown on header injection attempts.
     *
     * @param string $reason Reason that can be used as HTTP response reason.
     */
    public function __construct($reason)
    {
        if (!\is_string($reason)) {
            if (!(\is_string($reason) || \is_object($reason) && \method_exists($reason, '__toString') || (\is_bool($reason) || \is_numeric($reason)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($reason) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($reason) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $reason = (string) $reason;
            }
        }
        parent::__construct($reason);
    }
}