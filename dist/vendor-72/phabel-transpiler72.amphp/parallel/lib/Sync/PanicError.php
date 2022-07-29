<?php

namespace Amp\Parallel\Sync;

/**
 * @deprecated ContextPanicError will be thrown from uncaught exceptions in child processes and threads instead of
 * this class.
 */
class PanicError extends \Error
{
    /** @var string Class name of uncaught exception. */
    private $name;
    /** @var string Stack trace of the panic. */
    private $trace;
    /**
     * Creates a new panic error.
     *
     * @param string $name The uncaught exception class.
     * @param string $message The panic message.
     * @param string $trace The panic stack trace.
     * @param (\Throwable | null) $previous Previous exception.
     */
    public function __construct($name, $message = '', $trace = '', ?\Throwable $previous = NULL)
    {
        if (!\is_string($name)) {
            if (!(\is_string($name) || \is_object($name) && \method_exists($name, '__toString') || (\is_bool($name) || \is_numeric($name)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($name) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($name) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $name = (string) $name;
            }
        }
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!\is_string($trace)) {
            if (!(\is_string($trace) || \is_object($trace) && \method_exists($trace, '__toString') || (\is_bool($trace) || \is_numeric($trace)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($trace) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($trace) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $trace = (string) $trace;
            }
        }
        parent::__construct($message, 0, $previous);
        $this->name = $name;
        $this->trace = $trace;
    }
    /**
     * @deprecated Use ContextPanicError::getOriginalClassName() instead.
     *
     * Returns the class name of the uncaught exception.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * @deprecated Use ContextPanicError::getOriginalTraceAsString() instead.
     *
     * Gets the stack trace at the point the panic occurred.
     *
     * @return string
     */
    public function getPanicTrace() : string
    {
        return $this->trace;
    }
}