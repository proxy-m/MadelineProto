<?php

namespace Amp\Http\Http2;

final class Http2ConnectionException extends \Exception
{
    public function __construct(string $message, int $code, ?\Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
    }
}