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
namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LogmaticFormatter;
/**
 * @author Julien Breux <julien.breux@gmail.com>
 */
class LogmaticHandler extends SocketHandler
{
    /**
     * @var string
     */
    private $logToken;
    /**
     * @var string
     */
    private $hostname;
    /**
     * @var string
     */
    private $appname;
    /**
     * @param string $token Log token supplied by Logmatic.
     * @param string $hostname Host name supplied by Logmatic.
     * @param string $appname Application name supplied by Logmatic.
     * @param bool $useSSL Whether or not SSL encryption should be used.
     *
     * @throws MissingExtensionException If SSL encryption is set to true and OpenSSL is missing
     */
    public function __construct($token, $hostname = '', $appname = '', bool $useSSL = true, $level = 100, bool $bubble = true, bool $persistent = false, float $timeout = 0.0, float $writingTimeout = 10.0, ?float $connectionTimeout = NULL, ?int $chunkSize = NULL)
    {
        if (!\is_string($token)) {
            if (!(\is_string($token) || \is_object($token) && \method_exists($token, '__toString') || (\is_bool($token) || \is_numeric($token)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($token) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($token) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $token = (string) $token;
            }
        }
        if (!\is_string($hostname)) {
            if (!(\is_string($hostname) || \is_object($hostname) && \method_exists($hostname, '__toString') || (\is_bool($hostname) || \is_numeric($hostname)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($hostname) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($hostname) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $hostname = (string) $hostname;
            }
        }
        if (!\is_string($appname)) {
            if (!(\is_string($appname) || \is_object($appname) && \method_exists($appname, '__toString') || (\is_bool($appname) || \is_numeric($appname)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($appname) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($appname) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $appname = (string) $appname;
            }
        }
        if ($useSSL && !extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use SSL encrypted connection for LogmaticHandler');
        }
        $endpoint = $useSSL ? 'ssl://api.logmatic.io:10515' : 'api.logmatic.io:10514';
        $endpoint .= '/v1/';
        parent::__construct($endpoint, $level, $bubble, $persistent, $timeout, $writingTimeout, $connectionTimeout, $chunkSize);
        $this->logToken = $token;
        $this->hostname = $hostname;
        $this->appname = $appname;
    }
    /**
     * {@inheritDoc}
     */
    protected function generateDataStream(array $record) : string
    {
        return $this->logToken . ' ' . $record['formatted'];
    }
    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter() : FormatterInterface
    {
        $formatter = new LogmaticFormatter();
        if (!empty($this->hostname)) {
            $formatter->setHostname($this->hostname);
        }
        if (!empty($this->appname)) {
            $formatter->setAppname($this->appname);
        }
        return $formatter;
    }
}