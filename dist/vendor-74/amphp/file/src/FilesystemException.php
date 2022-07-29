<?php

namespace Amp\File;

class FilesystemException extends \Exception
{
    /**
     *
     */
    public function __construct(string $message, \Throwable $previous = NULL)
    {
        parent::__construct($message, 0, $previous);
    }
}