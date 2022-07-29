<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Lexer\TokenEmulator;

use PhabelVendor\PhpParser\Lexer\Emulative;
final class AttributeEmulator extends TokenEmulator
{
    /**
     *
     */
    public function getPhpVersion() : string
    {
        return Emulative::PHP_8_0;
    }
    /**
     *
     */
    public function isEmulationNeeded(string $code) : bool
    {
        return \strpos($code, '#[') !== \false;
    }
    /**
     *
     */
    public function emulate(string $code, array $tokens) : array
    {
        // We need to manually iterate and manage a count because we'll change
        // the tokens array on the way.
        $line = 1;
        for ($i = 0, $c = \count($tokens); $i < $c; ++$i) {
            if ($tokens[$i] === '#' && isset($tokens[$i + 1]) && $tokens[$i + 1] === '[') {
                \Phabel\Target\Php74\Polyfill::array_splice($tokens, $i, 2, [[\T_ATTRIBUTE, '#[', $line]]);
                $c--;
                continue;
            }
            if (\is_array($tokens[$i])) {
                $line += \Phabel\Target\Php80\Polyfill::substr_count($tokens[$i][1], "\n");
            }
        }
        return $tokens;
    }
    /**
     *
     */
    public function reverseEmulate(string $code, array $tokens) : array
    {
        // TODO
        return $tokens;
    }
    /**
     *
     */
    public function preprocessCode(string $code, array &$patches) : string
    {
        $pos = 0;
        while (\false !== ($pos = \strpos($code, '#[', $pos))) {
            // Replace #[ with %[
            $code[$pos] = '%';
            $patches[] = [$pos, 'replace', '#'];
            $pos += 2;
        }
        return $code;
    }
}