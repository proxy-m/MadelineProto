<?php

namespace Amp\Log;

use Amp\ByteStream\OutputStream;
use Monolog\Handler\AbstractProcessingHandler;
use Psr\Log\LogLevel;
final class StreamHandler extends AbstractProcessingHandler
{
    /** @var OutputStream */
    private $stream;
    /** @var callable */
    private $onResolve;
    /** @var \Throwable|null */
    private $exception;
    /**
     * @param OutputStream $outputStream
     * @param string $level
     * @param bool $bubble
     */
    public function __construct($outputStream, $level = 'debug', bool $bubble = true)
    {
        if (!$outputStream instanceof OutputStream) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($outputStream) must be of type OutputStream, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($outputStream) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!\is_string($level)) {
            if (!(\is_string($level) || \is_object($level) && \method_exists($level, '__toString') || (\is_bool($level) || \is_numeric($level)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($level) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($level) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $level = (string) $level;
            }
        }
        parent::__construct($level, $bubble);
        $this->stream = $outputStream;
        $stream =& $this->stream;
        $exception =& $this->exception;
        $this->onResolve = static function (\Throwable $e = NULL) use(&$stream, &$exception) {
            if (!$stream) {
                return;
                // Prior write already failed, ignore this failure.
            }
            if ($e) {
                $stream = null;
                $exception = $e;
                throw $e;
            }
        };
    }
    /**
     * Writes the record down to the log of the implementing handler.
     *
     * @param array $record
     *
     * @return void
     */
    protected function write(array $record) : void
    {
        if ($this->exception) {
            throw $this->exception;
        }
        $this->stream->write((string) $record['formatted'])->onResolve($this->onResolve);
    }
}