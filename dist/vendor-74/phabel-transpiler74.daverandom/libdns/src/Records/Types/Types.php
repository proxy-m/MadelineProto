<?php

declare (strict_types=1);
/**
 * Enumeration of simple data types
 *
 * PHP version 5.4
 *
 * @category LibDNS
 * @package Types
 * @author Chris Wright <https://github.com/DaveRandom>
 * @copyright Copyright (c) Chris Wright <https://github.com/DaveRandom>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @version 2.0.0
 */
namespace LibDNS\Records\Types;

use LibDNS\Enumeration;
/**
 * Enumeration of simple data types
 *
 * @category LibDNS
 * @package Types
 * @author Chris Wright <https://github.com/DaveRandom>
 */
final class Types extends Enumeration
{
    const ANYTHING = 1;
    const BITMAP = 2;
    const CHAR = 4;
    const CHARACTER_STRING = 8;
    const DOMAIN_NAME = 16;
    const IPV4_ADDRESS = 32;
    const IPV6_ADDRESS = 64;
    const LONG = 128;
    const SHORT = 256;
}