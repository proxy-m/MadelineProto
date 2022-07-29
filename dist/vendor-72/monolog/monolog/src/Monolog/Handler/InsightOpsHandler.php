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
 * Inspired on LogEntriesHandler.
 *
 * @author Robert Kaufmann III <rok3@rok3.me>
 * @author Gabriel Machado <gabriel.ms1@hotmail.com>
 */
class InsightOpsHandler extends SocketHandler
{
    /**
     * @var string
     */
    protected $logToken;
    /**
     * @param string $token Log token supplied by InsightOps
     * @param string $region Region where InsightOps account is hosted. Could be 'us' or 'eu'.
     * @param bool $useSSL Whether or not SSL encryption should be used
     *
     * @throws MissingExtensionException If SSL encryption is set to true and OpenSSL is missing
     * @param bool $bubble
     */
    public function __construct($token, $region = 'us', $useSSL = true, $level = 100, $bubble = true, bool $persistent = false, float $timeout = 0.0, float $writingTimeout = 10.0, ?float $connectionTimeout = NULL, ?int $chunkSize = NULL)
    {
        if (!\is_string($token)) {
            if (!(\is_string($token) || \is_object($token) && \method_exists($token, '__toString') || (\is_bool($token) || \is_numeric($token)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($token) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($token) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $token = (string) $token;
            }
        }
        if (!\is_string($region)) {
            if (!(\is_string($region) || \is_object($region) && \method_exists($region, '__toString') || (\is_bool($region) || \is_numeric($region)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($region) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($region) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $region = (string) $region;
            }
        }
        if (!\is_bool($useSSL)) {
            if (!(\is_bool($useSSL) || \is_numeric($useSSL) || \is_string($useSSL))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($useSSL) must be of type bool, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($useSSL) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $useSSL = (bool) $useSSL;
            }
        }
        if (!\is_bool($bubble)) {
            if (!(\is_bool($bubble) || \is_numeric($bubble) || \is_string($bubble))) {
                throw new \TypeError(__METHOD__ . '(): Argument #5 ($bubble) must be of type bool, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($bubble) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $bubble = (bool) $bubble;
            }
        }
        if ($useSSL && !extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP plugin is required to use SSL encrypted connection for InsightOpsHandler');
        }
        $endpoint = $useSSL ? 'ssl://' . $region . '.data.logs.insight.rapid7.com:443' : $region . '.data.logs.insight.rapid7.com:80';
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