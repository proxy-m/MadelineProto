<?php

namespace Amp\Http;

/**
 * Base class for HTTP request and response messages.
 */
abstract class Message
{
    /** @var string[][] */
    private $headers = [];
    /** @var string[][] */
    private $headerCase = [];
    /**
    * Returns the headers as a string-indexed array of arrays of strings or an empty array if no headers
    have been set.
    *
    * @return string[][]
    */
    public function getHeaders() : array
    {
        return $this->headers;
    }
    /**
     * Returns the headers as list of [field, name] pairs in the original casing provided by the application or server.
     *
     * @return array
     */
    public final function getRawHeaders() : array
    {
        $headers = [];
        foreach ($this->headers as $lcName => $values) {
            $size = \count($values);
            for ($i = 0; $i < $size; $i++) {
                $headers[] = [$this->headerCase[$lcName][$i], $values[$i]];
            }
        }
        return $headers;
    }
    /**
     * Returns the array of values for the given header or an empty array if the header does not exist.
     *
     * @param string $name
     *
     * @return string[]
     */
    public function getHeaderArray(string $name) : array
    {
        return $this->headers[\strtolower($name)] ?? [];
    }
    /**
    * Returns the value of the given header. If multiple headers are present for the named header, only the first
    header value will be returned. Use getHeaderArray() to return an array of all values for the particular header.
    Returns null if the header does not exist.
    *
    * @param string $name
    *
    * @return (string | null)
    */
    public function getHeader(string $name)
    {
        return $this->headers[\strtolower($name)][0] ?? null;
    }
    /**
     * Sets the headers from the given array.
     *
     * @param (string[] | string[][]) $headers
     */
    protected function setHeaders(array $headers)
    {
        // Ensure this is an atomic operation, either all headers are set or none.
        $before = $this->headers;
        $beforeCase = $this->headerCase;
        try {
            foreach ($headers as $name => $value) {
                $this->setHeader($name, $value);
            }
        } catch (\Throwable $e) {
            $this->headers = $before;
            $this->headerCase = $beforeCase;
            throw $e;
        }
    }
    /**
     * Sets the named header to the given value.
     *
     * @param string $name
     * @param (string | string[]) $value
     *
     * @throws \Error If the header name or value is invalid.
     */
    protected function setHeader(string $name, $value)
    {
        \assert($this->isNameValid($name), "Invalid header name");
        if (\is_array($value)) {
            if (!$value) {
                $this->removeHeader($name);
                return;
            }
            $value = \array_values(\array_map("strval", $value));
        } else {
            $value = [(string) $value];
        }
        \assert($this->isValueValid($value), "Invalid header value");
        $lcName = \strtolower($name);
        $this->headers[$lcName] = $value;
        $this->headerCase[$lcName] = [];
        foreach ($value as $_) {
            $this->headerCase[$lcName][] = $name;
        }
    }
    /**
     * Adds the value to the named header, or creates the header with the given value if it did not exist.
     *
     * @param string $name
     * @param (string | string[]) $value
     *
     * @throws \Error If the header name or value is invalid.
     */
    protected function addHeader(string $name, $value)
    {
        \assert($this->isNameValid($name), "Invalid header name");
        if (\is_array($value)) {
            if (!$value) {
                return;
            }
            $value = \array_values(\array_map("strval", $value));
        } else {
            $value = [(string) $value];
        }
        \assert($this->isValueValid($value), "Invalid header value");
        $lcName = \strtolower($name);
        if (isset($this->headers[$lcName])) {
            $this->headers[$lcName] = \array_merge($this->headers[$lcName], $value);
            foreach ($value as $_) {
                $this->headerCase[$lcName][] = $name;
            }
        } else {
            $this->headers[$lcName] = $value;
            foreach ($value as $_) {
                $this->headerCase[$lcName][] = $name;
            }
        }
    }
    /**
     * Removes the given header if it exists.
     *
     * @param string $name
     */
    protected function removeHeader(string $name)
    {
        $lcName = \strtolower($name);
        unset($this->headers[$lcName], $this->headerCase[$lcName]);
    }
    /**
     * Checks if given header exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader(string $name) : bool
    {
        return isset($this->headers[\strtolower($name)]);
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    private function isNameValid(string $name) : bool
    {
        return (bool) \preg_match('/^[A-Za-z0-9`~!#$%^&_|\'\\-:]+$/', $name);
    }
    /**
    * Determines if the given value is a valid header value.
    *
    * @param string[] $values
    *
    * @return bool
    *
    * @throws \Error If the given value cannot be converted to a string and is not an array of values that can be
    converted to strings.
    */
    private function isValueValid(array $values) : bool
    {
        foreach ($values as $value) {
            if (\preg_match("/[^\t\r\n -~\x80-\xfe]|\r\n/", $value)) {
                return false;
            }
        }
        return true;
    }
}