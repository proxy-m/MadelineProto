<?php

namespace Amp\Sql;

class QueryError extends \Error
{
    protected $query = "";
    /**
     *
     * @param string $message
     * @param string $query
     * @param (\Throwable | null) $previous
     */
    public function __construct($message, $query = '', $previous = NULL)
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!\is_string($query)) {
            if (!(\is_string($query) || \is_object($query) && \method_exists($query, '__toString') || (\is_bool($query) || \is_numeric($query)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($query) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($query) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $query = (string) $query;
            }
        }
        if (!($previous instanceof \Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #3 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if ($query !== "") {
            $this->query = $query;
        }
        parent::__construct($message, 0, $previous);
    }
    /**
     *
     */
    public final function getQuery() : string
    {
        return $this->query;
    }
    /**
     *
     */
    public function __toString() : string
    {
        if ($this->query === "") {
            return parent::__toString();
        }
        $msg = $this->message;
        $this->message .= "\nCurrent query was {$this->query}";
        $str = parent::__toString();
        $this->message = $msg;
        return $str;
    }
}