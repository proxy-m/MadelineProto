<?php

namespace Amp\Ipc;

/**
 * Thrown in case server connection fails.
 */
final class IpcServerException extends \Exception
{
    private const TYPE_MAP = [IpcServer::TYPE_UNIX => 'UNIX', IpcServer::TYPE_TCP => 'TCP', IpcServer::TYPE_FIFO => 'FIFO'];
    /**
     *
     * @param array $messages
     * @param int $code
     * @param (\Throwable | null) $previous
     */
    public function __construct($messages, $code = 0, $previous = NULL)
    {
        if (!\is_array($messages)) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($messages) must be of type array, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($messages) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
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
        $message = "Could not create IPC server: ";
        foreach ($messages as $type => $error) {
            $message .= self::TYPE_MAP[$type] . ": {$error}; ";
        }
        parent::__construct($message, $code, $previous);
    }
}