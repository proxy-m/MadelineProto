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
/**
 * @author Robert Kaufmann III <rok3@rok3.me>
 */
class LogEntriesHandler extends SocketHandler
{
    /**
     * @var string
     */
    protected $logToken;
    /**
     * @param string $token Log token supplied by LogEntries
     * @param bool $useSSL Whether or not SSL encryption should be used.
     * @param string $host Custom hostname to send the data to if needed
     *
     * @throws MissingExtensionException If SSL encryption is set to true and OpenSSL is missing
     */
    public function __construct($token, $useSSL = true, $level = 100, bool $bubble = true, $host = 'data.logentries.com', bool $persistent = false, float $timeout = 0.0, float $writingTimeout = 10.0, ?float $connectionTimeout = NULL, ?int $chunkSize = NULL)
    {
        if (!\is_string($token)) {
            if (!(\is_string($token) || \is_object($token) && \method_exists($token, '__toString') || (\is_bool($token) || \is_numeric($token)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($token) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($token) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $token = (string) $token;
            }
        }
        if (!\is_bool($useSSL)) {
            if (!(\is_bool($useSSL) || \is_numeric($useSSL) || \is_string($useSSL))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($useSSL) must be of type bool, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($useSSL) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $useSSL = (bool) $useSSL;
            }
        }
        if (!\is_string($host)) {
            if (!(\is_string($host) || \is_object($host) && \method_exists($host, '__toString') || (\is_bool($host) || \is_numeric($host)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #5 ($host) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($host) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $host = (string) $host;
            }
        }
        if ($useSSL && !extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP plugin is required to use SSL encrypted connection for LogEntriesHandler');
        }
        $endpoint = $useSSL ? 'ssl://' . $host . ':443' : $host . ':80';
        parent::__construct($endpoint, $level, $bubble, $persistent, $timeout, $writingTimeout, $connectionTimeout, $chunkSize);
        $this->logToken = $token;
    }
    /**
     * {@inheritDoc}
     */
    protected function generateDataStream(array $record) : string
    {
        return $this->logToken . ' ' . $record['formatted'];
    }
}