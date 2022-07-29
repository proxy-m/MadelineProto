<?php

namespace Amp\Dns;

use Throwable;
/**
 * MUST be thrown in case the config can't be read and no fallback is available.
 */
class ConfigException extends DnsException
{
    /**
     *
     * @param string $message
     * @param (Throwable | null) $previous
     */
    public function __construct($message, $previous = NULL)
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!($previous instanceof Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct($message, 0, $previous);
    }
}