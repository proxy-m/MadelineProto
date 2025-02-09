<?php

/**
 * PuTTY Formatted Key Handler
 *
 * PHP version 5
 *
 * @category  Crypt
 * @package   Common
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace tgseclib\Crypt\Common\Formats\Keys;

use ParagonIE\ConstantTime\Base64;
use ParagonIE\ConstantTime\Hex;
use tgseclib\Crypt\AES;
use tgseclib\Crypt\Hash;
use tgseclib\Crypt\Random;
use tgseclib\Common\Functions\Strings;
use tgseclib\Exception\UnsupportedAlgorithmException;
/**
 * PuTTY Formatted Key Handler
 *
 * @package Common
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class PuTTY
{
    /**
     * Default comment
     *
     * @var string
     * @access private
     */
    private static $comment = 'phpseclib-generated-key';
    /**
     * Sets the default comment
     *
     * @access public
     * @param string $comment
     */
    public static function setComment($comment)
    {
        self::$comment = str_replace(["\r", "\n"], '', $comment);
    }
    /**
     * Generate a symmetric key for PuTTY keys
     *
     * @access public
     * @param string $password
     * @param int $length
     * @return string
     */
    private static function generateSymmetricKey($password, $length)
    {
        $symkey = '';
        $sequence = 0;
        while (strlen($symkey) < $length) {
            $temp = pack('Na*', $sequence++, $password);
            $symkey .= Hex::decode(sha1($temp));
        }
        return substr($symkey, 0, $length);
    }
    /**
     * Break a public or private key down into its constituent components
     *
     * @access public
     * @param string $key
     * @param string $password
     * @return array
     */
    protected static function load($key, $password)
    {
        if (!is_string($key)) {
            throw new \UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }
        if (strpos($key, 'BEGIN SSH2 PUBLIC KEY') !== false) {
            $data = preg_split('#[\\r\\n]+#', $key);
            $data = array_splice($data, 2, -1);
            $data = implode('', $data);
            $components = call_user_func([static::PUBLIC_HANDLER, 'load'], $data);
            if ($components === false) {
                throw new \UnexpectedValueException('Unable to decode public key');
            }
            if (!preg_match('#Comment: "(.+)"#', $key, $matches)) {
                throw new \UnexpectedValueException('Key is missing a comment');
            }
            $components['comment'] = str_replace(['\\\\', '\\"'], ['\\', '"'], $matches[1]);
            return $components;
        }
        $components = [];
        $key = preg_split('#\\r\\n|\\r|\\n#', trim($key));
        $type = trim(preg_replace('#PuTTY-User-Key-File-2: (.+)#', '$1', $key[0]));
        $components['type'] = $type;
        if (!in_array($type, static::$types)) {
            $error = count(static::$types) == 1 ? 'Only ' . static::$types[0] . ' keys are supported. ' : '';
            throw new UnsupportedAlgorithmException($error . 'This is an unsupported ' . $type . ' key');
        }
        $encryption = trim(preg_replace('#Encryption: (.+)#', '$1', $key[1]));
        $components['comment'] = trim(preg_replace('#Comment: (.+)#', '$1', $key[2]));
        $publicLength = trim(preg_replace('#Public-Lines: (\\d+)#', '$1', $key[3]));
        $public = Base64::decode(implode('', array_map('trim', array_slice($key, 4, $publicLength))));
        $source = Strings::packSSH2('ssss', $type, $encryption, $components['comment'], $public);
        extract(unpack('Nlength', Strings::shift($public, 4)));
        $newtype = Strings::shift($public, $length);
        if ($newtype != $type) {
            throw new \RuntimeException('The binary type does not match the human readable type field');
        }
        $components['public'] = $public;
        $privateLength = trim(preg_replace('#Private-Lines: (\\d+)#', '$1', $key[$publicLength + 4]));
        $private = Base64::decode(implode('', array_map('trim', array_slice($key, $publicLength + 5, $privateLength))));
        switch ($encryption) {
            case 'aes256-cbc':
                $symkey = self::generateSymmetricKey($password, 32);
                $crypto = new AES('cbc');
        }
        $hashkey = 'putty-private-key-file-mac-key';
        if ($encryption != 'none') {
            $hashkey .= $password;
            $crypto->setKey($symkey);
            $crypto->setIV(str_repeat("\x00", $crypto->getBlockLength() >> 3));
            $crypto->disablePadding();
            $private = $crypto->decrypt($private);
        }
        $source .= Strings::packSSH2('s', $private);
        $hash = new Hash('sha1');
        $hash->setKey(sha1($hashkey, true));
        $hmac = trim(preg_replace('#Private-MAC: (.+)#', '$1', $key[$publicLength + $privateLength + 5]));
        $hmac = Hex::decode($hmac);
        if (!hash_equals($hash->hash($source), $hmac)) {
            throw new \UnexpectedValueException('MAC validation error');
        }
        $components['private'] = $private;
        return $components;
    }
    /**
     * Wrap a private key appropriately
     *
     * @access private
     * @param string $public
     * @param string $private
     * @param string $type
     * @param string $password
     * @param array $options optional
     * @return string
     */
    protected static function wrapPrivateKey($public, $private, $type, $password, array $options = array())
    {
        $key = "PuTTY-User-Key-File-2: " . $type . "\r\nEncryption: ";
        $encryption = !empty($password) || is_string($password) ? 'aes256-cbc' : 'none';
        $key .= $encryption;
        $key .= "\r\nComment: " . self::$comment . "\r\n";
        $public = Strings::packSSH2('s', $type) . $public;
        $comment = isset($options['comment']) ? $options['comment'] : self::$comment;
        $source = Strings::packSSH2('ssss', $type, $encryption, $comment, $public);
        $public = Base64::encode($public);
        $key .= "Public-Lines: " . (strlen($public) + 63 >> 6) . "\r\n";
        $key .= chunk_split($public, 64);
        if (empty($password) && !is_string($password)) {
            $source .= Strings::packSSH2('s', $private);
            $hashkey = 'putty-private-key-file-mac-key';
        } else {
            $private .= Random::string(16 - (strlen($private) & 15));
            $source .= Strings::packSSH2('s', $private);
            $crypto = new AES('cbc');
            $crypto->setKey(self::generateSymmetricKey($password, 32));
            $crypto->setIV(str_repeat("\x00", $crypto->getBlockLength() >> 3));
            $crypto->disablePadding();
            $private = $crypto->encrypt($private);
            $hashkey = 'putty-private-key-file-mac-key' . $password;
        }
        $private = Base64::encode($private);
        $key .= 'Private-Lines: ' . (strlen($private) + 63 >> 6) . "\r\n";
        $key .= chunk_split($private, 64);
        $hash = new Hash('sha1');
        $hash->setKey(sha1($hashkey, true));
        $key .= 'Private-MAC: ' . Hex::encode($hash->hash($source)) . "\r\n";
        return $key;
    }
    /**
     * Wrap a public key appropriately
     *
     * This is basically the format described in RFC 4716 (https://tools.ietf.org/html/rfc4716)
     *
     * @access private
     * @param string $key
     * @param string $type
     * @return string
     */
    protected static function wrapPublicKey($key, $type)
    {
        $key = pack('Na*a*', strlen($type), $type, $key);
        $key = "---- BEGIN SSH2 PUBLIC KEY ----\r\n" . 'Comment: "' . str_replace(['\\', '"'], ['\\\\', '\\"'], self::$comment) . "\"\r\n" . chunk_split(Base64::encode($key), 64) . '---- END SSH2 PUBLIC KEY ----';
        return $key;
    }
}