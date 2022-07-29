<?php

namespace Amp;

class MultiReasonException extends \Exception
{
    /** @var \Throwable[] */
    private $reasons;
    /**
     * @param \Throwable[] $reasons Array of exceptions rejecting the promise.
     * @param (string | null) $message
     */
    public function __construct($reasons, $message = NULL)
    {
        if (!\is_array($reasons)) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($reasons) must be of type array, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($reasons) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!(\is_string($message) || \is_null($message))) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($message) must be of type ?string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        parent::__construct($message ?: "Multiple errors encountered; use " . self::class . "::getReasons() to retrieve the array of exceptions thrown");
        $this->reasons = $reasons;
    }
    /**
     * @return \Throwable[]
     */
    public function getReasons() : array
    {
        return $this->reasons;
    }
}