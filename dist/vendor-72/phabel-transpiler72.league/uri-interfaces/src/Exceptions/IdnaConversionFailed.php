<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare (strict_types=1);
namespace League\Uri\Exceptions;

use League\Uri\Idna\IdnaInfo;
final class IdnaConversionFailed extends SyntaxError
{
    /** @var IdnaInfo|null  */
    private $idnaInfo;
    /**
     *
     * @param string $message
     * @param (IdnaInfo | null) $idnaInfo
     */
    private function __construct($message, $idnaInfo = NULL)
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!($idnaInfo instanceof IdnaInfo || \is_null($idnaInfo))) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($idnaInfo) must be of type ?IdnaInfo, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($idnaInfo) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct($message);
        $this->idnaInfo = $idnaInfo;
    }
    /**
     *
     */
    public static function dueToIDNAError(string $domain, IdnaInfo $idnaInfo) : self
    {
        return new self('The host `' . $domain . '` is invalid : ' . implode(', ', $idnaInfo->errorList()) . ' .', $idnaInfo);
    }
    /**
     *
     */
    public static function dueToInvalidHost(string $domain) : self
    {
        return new self('The host `' . $domain . '` is not a valid IDN host');
    }
    /**
     *
     */
    public function idnaInfo() : ?IdnaInfo
    {
        return $this->idnaInfo;
    }
}