<?php

namespace Psr\Log;

/**
 * This is a simple Logger trait that classes unable to extend AbstractLogger
 * (because they extend another class, etc) can include.
 *
 * It simply delegates all log-level-specific methods to the `log` method to
 * reduce boilerplate code that a simple Logger that does the same thing with
 * messages regardless of the error level has to implement.
 */
trait LoggerTrait
{
    /**
     * System is unusable.
     *
     * @param (string | \Stringable) $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }
    /**
    * Action must be taken immediately.
    *
    * Example: Entire website down, database unavailable, etc. This should
    trigger the SMS alerts and wake you up.
    *
    * @param (string | \Stringable) $message
    * @param array $context
    *
    * @return void
    */
    public function alert($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::ALERT, $message, $context);
    }
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param (string | \Stringable) $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::CRITICAL, $message, $context);
    }
    /**
    * Runtime errors that do not require immediate action but should typically
    be logged and monitored.
    *
    * @param (string | \Stringable) $message
    * @param array $context
    *
    * @return void
    */
    public function error($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::ERROR, $message, $context);
    }
    /**
    * Exceptional occurrences that are not errors.
    *
    * Example: Use of deprecated APIs, poor use of an API, undesirable things
    that are not necessarily wrong.
    *
    * @param (string | \Stringable) $message
    * @param array $context
    *
    * @return void
    */
    public function warning($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::WARNING, $message, $context);
    }
    /**
     * Normal but significant events.
     *
     * @param (string | \Stringable) $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::NOTICE, $message, $context);
    }
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param (string | \Stringable) $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::INFO, $message, $context);
    }
    /**
     * Detailed debug information.
     *
     * @param (string | \Stringable) $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = array()) : void
    {
        if (!(\is_string($message) || $message instanceof \Stringable)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type Stringable|string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        $this->log(LogLevel::DEBUG, $message, $context);
    }
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param (string | \Stringable) $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public abstract function log($level, $message, array $context = array()) : void;
}