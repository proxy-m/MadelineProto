<?php

namespace Amp\Websocket;

final class Opcode
{
    public const CONT = 0;
    public const TEXT = 1;
    public const BIN = 2;
    public const CLOSE = 8;
    public const PING = 9;
    public const PONG = 10;
    /**
     * @codeCoverageIgnore Class cannot be instigated.
     */
    private function __construct()
    {
        // forbid instances
    }
}