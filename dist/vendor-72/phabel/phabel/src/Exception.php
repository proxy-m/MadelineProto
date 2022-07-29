<?php

namespace Phabel;

/**
 * Exception.
 */
class Exception extends \Exception
{
    /**
     * @var (string | null) $trace
     */
    private $trace;
    /**
     * Get trace.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->trace ?? parent::__toString();
    }
    /**
     * Constructor.
     *
     * @param string $message
     * @param integer $code
     * @param \Throwable $previous
     * @param string $file
     * @param int $line
     */
    public function __construct($message = '', $code = 0, $previous = NULL, string $file = '', int $line = -1)
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
        if ($file !== '') {
            $this->file = $file;
        }
        if ($line !== -1) {
            $this->line = $line;
        }
        parent::__construct($message, $code, $previous);
    }
    /**
     * Set the value of trace.
     *
     * @param ?string $trace
     *
     * @return self
     */
    public function setTrace(?string $trace) : self
    {
        $this->trace = $trace;
        return $this;
    }
}