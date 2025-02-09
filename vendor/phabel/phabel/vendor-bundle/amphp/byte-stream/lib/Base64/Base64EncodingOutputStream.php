<?php

namespace PhabelVendor\Amp\ByteStream\Base64;

use PhabelVendor\Amp\ByteStream\OutputStream;
use PhabelVendor\Amp\Promise;
final class Base64EncodingOutputStream implements OutputStream
{
    /** @var OutputStream */
    private $destination;
    /** @var string */
    private $buffer = '';
    /**
     *
     */
    public function __construct(OutputStream $destination)
    {
        $this->destination = $destination;
    }
    /**
     *
     */
    public function write(string $data) : Promise
    {
        $this->buffer .= $data;
        $length = \strlen($this->buffer);
        $chunk = \base64_encode(\Phabel\Target\Php80\Polyfill::substr($this->buffer, 0, $length - $length % 3));
        $this->buffer = \Phabel\Target\Php80\Polyfill::substr($this->buffer, $length - $length % 3);
        return $this->destination->write($chunk);
    }
    /**
     *
     */
    public function end(string $finalData = '') : Promise
    {
        $chunk = \base64_encode($this->buffer . $finalData);
        $this->buffer = '';
        return $this->destination->end($chunk);
    }
}