<?php

namespace Amp\Postgres;

use Amp\Sql\QueryError;
class QueryExecutionError extends QueryError
{
    /** @var mixed[] */
    private $diagnostics;
    /**
     *
     * @param string $message
     * @param array $diagnostics
     * @param (\Throwable | null) $previous
     */
    public function __construct($message, $diagnostics, $previous = NULL, string $query = '')
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!\is_array($diagnostics)) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($diagnostics) must be of type array, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($diagnostics) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!($previous instanceof \Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #3 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct($message, $query, $previous);
        $this->diagnostics = $diagnostics;
    }
    /**
     *
     */
    public function getDiagnostics() : array
    {
        return $this->diagnostics;
    }
}