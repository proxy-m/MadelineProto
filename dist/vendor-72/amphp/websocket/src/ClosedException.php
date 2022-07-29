<?php

namespace Amp\Websocket;

final class ClosedException extends \Exception
{
    /** @var string */
    private $reason;
    /**
     *
     * @param string $message
     * @param int $code
     * @param string $reason
     */
    public function __construct($message, $code, $reason)
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
        if (!\is_string($reason)) {
            if (!(\is_string($reason) || \is_object($reason) && \method_exists($reason, '__toString') || (\is_bool($reason) || \is_numeric($reason)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($reason) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($reason) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $reason = (string) $reason;
            }
        }
        parent::__construct(\sprintf('%s; Code %s (%s); Reason: "%s"', $message, $code, Code::getName($code) ?? 'Unknown code', $reason), $code);
        $this->reason = $reason;
    }
    /**
     *
     */
    public function getReason() : string
    {
        return $this->reason;
    }
}