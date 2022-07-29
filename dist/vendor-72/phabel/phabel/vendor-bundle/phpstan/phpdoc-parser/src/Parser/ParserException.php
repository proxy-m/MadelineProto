<?php

declare (strict_types=1);
namespace PhabelVendor\PHPStan\PhpDocParser\Parser;

use Exception;
use PhabelVendor\PHPStan\PhpDocParser\Lexer\Lexer;
use function assert;
use function json_encode;
use function sprintf;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
class ParserException extends Exception
{
    /** @var string */
    private $currentTokenValue;
    /** @var int */
    private $currentTokenType;
    /** @var int */
    private $currentOffset;
    /** @var int */
    private $expectedTokenType;
    /** @var string|null */
    private $expectedTokenValue;
    /**
     *
     * @param string $currentTokenValue
     * @param int $currentTokenType
     * @param int $currentOffset
     */
    public function __construct($currentTokenValue, $currentTokenType, $currentOffset, int $expectedTokenType, ?string $expectedTokenValue = NULL)
    {
        if (!\is_string($currentTokenValue)) {
            if (!(\is_string($currentTokenValue) || \is_object($currentTokenValue) && \method_exists($currentTokenValue, '__toString') || (\is_bool($currentTokenValue) || \is_numeric($currentTokenValue)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($currentTokenValue) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($currentTokenValue) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $currentTokenValue = (string) $currentTokenValue;
            }
        }
        if (!\is_int($currentTokenType)) {
            if (!(\is_bool($currentTokenType) || \is_numeric($currentTokenType))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($currentTokenType) must be of type int, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($currentTokenType) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $currentTokenType = (int) $currentTokenType;
            }
        }
        if (!\is_int($currentOffset)) {
            if (!(\is_bool($currentOffset) || \is_numeric($currentOffset))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($currentOffset) must be of type int, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($currentOffset) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $currentOffset = (int) $currentOffset;
            }
        }
        $this->currentTokenValue = $currentTokenValue;
        $this->currentTokenType = $currentTokenType;
        $this->currentOffset = $currentOffset;
        $this->expectedTokenType = $expectedTokenType;
        $this->expectedTokenValue = $expectedTokenValue;
        parent::__construct(sprintf('Unexpected token %s, expected %s%s at offset %d', $this->formatValue($currentTokenValue), Lexer::TOKEN_LABELS[$expectedTokenType], $expectedTokenValue !== null ? sprintf(' (%s)', $this->formatValue($expectedTokenValue)) : '', $currentOffset));
    }
    /**
     *
     */
    public function getCurrentTokenValue() : string
    {
        return $this->currentTokenValue;
    }
    /**
     *
     */
    public function getCurrentTokenType() : int
    {
        return $this->currentTokenType;
    }
    /**
     *
     */
    public function getCurrentOffset() : int
    {
        return $this->currentOffset;
    }
    /**
     *
     */
    public function getExpectedTokenType() : int
    {
        return $this->expectedTokenType;
    }
    /**
     *
     */
    public function getExpectedTokenValue() : ?string
    {
        return $this->expectedTokenValue;
    }
    /**
     *
     */
    private function formatValue(string $value) : string
    {
        $json = json_encode($value, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        assert($json !== \false);
        return $json;
    }
}