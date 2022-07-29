<?php

namespace Amp\Postgres;

use Amp\Sql\FailureException;
class ParseException extends FailureException
{
    /**
     *
     * @param string $message
     */
    public function __construct($message = '')
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $message = "Parse error while splitting array" . ($message === '' ? '' : ": " . $message);
        parent::__construct($message);
    }
}