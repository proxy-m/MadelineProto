<?php

namespace Amp\Websocket;

final class Opcode
{
    public const CONT = 0x0;
    public const TEXT = 0x1;
    public const BIN = 0x2;
    public const CLOSE = 0x8;
    public const PING = 0x9;
    public const PONG = 0xa;
    /**
     * @codeCoverageIgnore Class cannot be instigated.
     */
    private function __construct()
    {
        // forbid instances
    }
}