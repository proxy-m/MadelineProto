<?php

namespace Amp\Parallel\Sync;

final class ContextPanicError extends PanicError
{
    /** @var string */
    private $originalMessage;
    /** @var int|string */
    private $originalCode;
    /** @var string[] */
    private $originalTrace;
    /**
     * @param string $className Original exception class name.
     * @param string $message Original exception message.
     * @param (int | string) $code Original exception code.
     * @param array $trace Backtrace generated by {@see formatFlattenedBacktrace()}.
     * @param (self | null) $previous Instance representing any previous exception thrown in the child process or thread.
     */
    public function __construct(string $className, string $message, $code, array $trace, ?self $previous = NULL)
    {
        $format = 'Uncaught %s in child process or thread with message "%s" and code "%s"; use %s::getOriginalTrace() for the stack trace in the child process or thread';
        parent::__construct($className, \sprintf($format, $className, $message, $code, self::class), formatFlattenedBacktrace($trace), $previous);
        $this->originalMessage = $message;
        $this->originalCode = $code;
        $this->originalTrace = $trace;
    }
    /**
     * @return string Original exception class name.
     */
    public function getOriginalClassName() : string
    {
        return $this->getName();
    }
    /**
     * @return string Original exception message.
     */
    public function getOriginalMessage() : string
    {
        return $this->originalMessage;
    }
    /**
     * @return (int | string) Original exception code.
     */
    public function getOriginalCode()
    {
        return $this->originalCode;
    }
    /**
     * Original exception stack trace.
     *
     * @return array Same as {@see Throwable::getTrace()}, except all function arguments are formatted as strings.
     */
    public function getOriginalTrace() : array
    {
        return $this->originalTrace;
    }
    /**
     * Original backtrace flattened to a human-readable string.
     *
     * @return string
     */
    public function getOriginalTraceAsString() : string
    {
        return $this->getPanicTrace();
    }
}