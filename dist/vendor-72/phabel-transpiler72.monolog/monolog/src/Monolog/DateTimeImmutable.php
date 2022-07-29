<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Monolog;

use DateTimeZone;
/**
 * Overrides default json encoding of date time objects
 *
 * @author Menno Holtkamp
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class DateTimeImmutable extends \DateTimeImmutable implements \JsonSerializable
{
    /**
     * @var bool
     */
    private $useMicroseconds;
    /**
     *
     * @param bool $useMicroseconds
     * @param (DateTimeZone | null) $timezone
     */
    public function __construct($useMicroseconds, $timezone = NULL)
    {
        if (!\is_bool($useMicroseconds)) {
            if (!(\is_bool($useMicroseconds) || \is_numeric($useMicroseconds) || \is_string($useMicroseconds))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($useMicroseconds) must be of type bool, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($useMicroseconds) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $useMicroseconds = (bool) $useMicroseconds;
            }
        }
        if (!($timezone instanceof DateTimeZone || \is_null($timezone))) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($timezone) must be of type ?DateTimeZone, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($timezone) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        $this->useMicroseconds = $useMicroseconds;
        parent::__construct('now', $timezone);
    }
    /**
     *
     */
    public function jsonSerialize() : string
    {
        if ($this->useMicroseconds) {
            return $this->format('Y-m-d\\TH:i:s.uP');
        }
        return $this->format('Y-m-d\\TH:i:sP');
    }
    /**
     *
     */
    public function __toString() : string
    {
        return $this->jsonSerialize();
    }
}