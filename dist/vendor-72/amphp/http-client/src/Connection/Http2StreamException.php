<?php

namespace Amp\Http\Client\Connection;

use Amp\Http\Client\HttpException;
/**
 * @deprecated Exception moved to amphp/http. Catch the base exception class (HttpException) instead.
 */
final class Http2StreamException extends HttpException
{
    /** @var int */
    private $streamId;
    /**
     *
     * @param string $message
     * @param int $streamId
     * @param int $code
     */
    public function __construct($message, $streamId, $code, ?\Throwable $previous = NULL)
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!\is_int($streamId)) {
            if (!(\is_bool($streamId) || \is_numeric($streamId))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($streamId) must be of type int, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($streamId) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $streamId = (int) $streamId;
            }
        }
        if (!\is_int($code)) {
            if (!(\is_bool($code) || \is_numeric($code))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($code) must be of type int, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($code) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $code = (int) $code;
            }
        }
        parent::__construct($message, $code, $previous);
        $this->streamId = $streamId;
    }
    /**
     *
     */
    public function getStreamId() : int
    {
        return $this->streamId;
    }
}