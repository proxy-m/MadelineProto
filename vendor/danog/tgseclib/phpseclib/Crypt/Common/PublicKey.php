<?php

/**
 * PublicKey interface
 *
 * @category  Crypt
 * @package   Common
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2009 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace tgseclib\Crypt\Common;

/**
 * PublicKey interface
 *
 * @package Common
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
interface PublicKey
{
    public function verify($message, $signature);
    //public function encrypt($plaintext);
    public function toString($type, array $options = array());
    public function getFingerprint($algorithm);
}