<?php

namespace Amp\Http\Client\Connection;

use Amp\Http\Client\HttpException;
final class UnprocessedRequestException extends HttpException
{
    /**
     *
     * @param HttpException $previous
     */
    public function __construct($previous)
    {
        if (!$previous instanceof HttpException) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($previous) must be of type HttpException, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct("The request was not processed and can be safely retried", 0, $previous);
    }
}