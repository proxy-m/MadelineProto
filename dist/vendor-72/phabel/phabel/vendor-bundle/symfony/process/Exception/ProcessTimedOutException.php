<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PhabelVendor\Symfony\Component\Process\Exception;

use PhabelVendor\Symfony\Component\Process\Process;
/**
 * Exception that is thrown when a process times out.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ProcessTimedOutException extends RuntimeException
{
    public const TYPE_GENERAL = 1;
    public const TYPE_IDLE = 2;
    private $process;
    private $timeoutType;
    /**
     *
     * @param Process $process
     * @param int $timeoutType
     */
    public function __construct($process, $timeoutType)
    {
        if (!$process instanceof Process) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($process) must be of type Process, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($process) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!\is_int($timeoutType)) {
            if (!(\is_bool($timeoutType) || \is_numeric($timeoutType))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($timeoutType) must be of type int, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($timeoutType) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $timeoutType = (int) $timeoutType;
            }
        }
        $this->process = $process;
        $this->timeoutType = $timeoutType;
        parent::__construct(\sprintf('The process "%s" exceeded the timeout of %s seconds.', $process->getCommandLine(), $this->getExceededTimeout()));
    }
    /**
     *
     */
    public function getProcess()
    {
        return $this->process;
    }
    /**
     *
     */
    public function isGeneralTimeout()
    {
        return self::TYPE_GENERAL === $this->timeoutType;
    }
    /**
     *
     */
    public function isIdleTimeout()
    {
        return self::TYPE_IDLE === $this->timeoutType;
    }
    /**
     *
     */
    public function getExceededTimeout()
    {
        switch ($this->timeoutType) {
            case self::TYPE_GENERAL:
                return $this->process->getTimeout();
            case self::TYPE_IDLE:
                return $this->process->getIdleTimeout();
            default:
                throw new \LogicException(\sprintf('Unknown timeout type "%d".', $this->timeoutType));
        }
    }
}