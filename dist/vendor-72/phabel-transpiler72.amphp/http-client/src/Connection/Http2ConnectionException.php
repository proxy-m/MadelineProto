<?php

namespace Amp\Http\Client\Connection;

use Amp\Http\Client\HttpException;
/**
 * @deprecated Exception moved to amphp/http. Catch the base exception class (HttpException) instead.
 */
final class Http2ConnectionException extends HttpException
{
    /**
     *
     * @param string $message
     * @param int $code
     * @param (\Throwable | null) $previous
     */
    public function __construct($message, $code, $previous = NULL)
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!\is_int($code)) {
            if (!(\is_bool($code) || \is_numeric($code))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($code) must be of type int, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($code) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $code = (int) $code;
            }
        }
        if (!($previous instanceof \Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #3 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct($message, $code, $previous);
    }
}