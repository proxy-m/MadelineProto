<?php

namespace PhabelVendor\Amp\ByteStream\Base64;

use PhabelVendor\Amp\ByteStream\InputStream;
use PhabelVendor\Amp\ByteStream\StreamException;
use PhabelVendor\Amp\Promise;
use function PhabelVendor\Amp\call;
final class Base64DecodingInputStream implements InputStream
{
    /** @var InputStream|null */
    private $source;
    /** @var string|null */
    private $buffer = '';
    /**
     *
     */
    public function __construct(InputStream $source)
    {
        $this->source = $source;
    }
    /**
     *
     */
    public function read() : Promise
    {
        return call(function () {
            if ($this->source === null) {
                throw new StreamException('Failed to read stream chunk due to invalid base64 data');
            }
            $chunk = (yield $this->source->read());
            if ($chunk === null) {
                if ($this->buffer === null) {
                    return null;
                }
                $chunk = \base64_decode($this->buffer, \true);
                if ($chunk === \false) {
                    $this->source = null;
                    $this->buffer = null;
                    throw new StreamException('Failed to read stream chunk due to invalid base64 data');
                }
                $this->buffer = null;
                return $chunk;
            }
            $this->buffer .= $chunk;
            $length = \strlen($this->buffer);
            $chunk = \base64_decode(\Phabel\Target\Php80\Polyfill::substr($this->buffer, 0, $length - $length % 4), \true);
            if ($chunk === \false) {
                $this->source = null;
                $this->buffer = null;
                throw new StreamException('Failed to read stream chunk due to invalid base64 data');
            }
            $this->buffer = \Phabel\Target\Php80\Polyfill::substr($this->buffer, $length - $length % 4);
            return $chunk;
        });
    }
}