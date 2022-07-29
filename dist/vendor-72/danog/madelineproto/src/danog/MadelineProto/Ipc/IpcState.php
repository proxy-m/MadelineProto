<?php

namespace danog\MadelineProto\Ipc;

/**
 * IPC state class.
 */
final class IpcState
{
    /**
     * Startup time.
     * @var float $startupTime
     */
    private $startupTime;
    /**
     * Startup ID.
     * @var int $startupId
     */
    private $startupId;
    /**
     * Exception.
     * @var (ExitFailure | null) $exception
     */
    private $exception;
    /**
     * Construct.
     *
     * @param integer $startupId
     * @param \Throwable $exception
     */
    public function __construct(int $startupId, \Throwable $exception = NULL)
    {
        $this->startupTime = \microtime(true);
        $this->startupId = $startupId;
        $this->exception = $exception ? new ExitFailure($exception) : null;
    }
    /**
     * Get startup time.
     *
     * @return float
     */
    public function getStartupTime() : float
    {
        return $this->startupTime;
    }
    /**
     * Get startup ID.
     *
     * @return int
     */
    public function getStartupId() : int
    {
        return $this->startupId;
    }
    /**
     * Get exception.
     *
     * @return ?\Throwable
     */
    public function getException() : ?\Throwable
    {
        return $this->exception ? $this->exception->getException() : null;
    }
}