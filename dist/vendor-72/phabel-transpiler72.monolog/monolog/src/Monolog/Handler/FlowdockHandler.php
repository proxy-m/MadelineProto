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
use Monolog\Utils;
use Monolog\Formatter\FlowdockFormatter;
use Monolog\Formatter\FormatterInterface;
/**
 * Sends notifications through the Flowdock push API
 *
 * This must be configured with a FlowdockFormatter instance via setFormatter()
 *
 * Notes:
 * API token - Flowdock API token
 *
 * @author Dominik Liebler <liebler.dominik@gmail.com>
 * @see https://www.flowdock.com/api/push
 *
 * @phpstan-import-type FormattedRecord from AbstractProcessingHandler
 */
class FlowdockHandler extends SocketHandler
{
    /**
     * @var string
     */
    protected $apiToken;
    /**
     * @throws MissingExtensionException if OpenSSL is missing
     * @param string $apiToken
     * @param bool $bubble
     * @param float $timeout
     */
    public function __construct($apiToken, $level = 100, $bubble = true, bool $persistent = false, $timeout = 0.0, float $writingTimeout = 10.0, ?float $connectionTimeout = NULL, ?int $chunkSize = NULL)
    {
        if (!\is_string($apiToken)) {
            if (!(\is_string($apiToken) || \is_object($apiToken) && \method_exists($apiToken, '__toString') || (\is_bool($apiToken) || \is_numeric($apiToken)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($apiToken) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($apiToken) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $apiToken = (string) $apiToken;
            }
        }
        if (!\is_bool($bubble)) {
            if (!(\is_bool($bubble) || \is_numeric($bubble) || \is_string($bubble))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($bubble) must be of type bool, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($bubble) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $bubble = (bool) $bubble;
            }
        }
        if (!\is_float($timeout)) {
            if (!(\is_bool($timeout) || \is_numeric($timeout))) {
                throw new \TypeError(__METHOD__ . '(): Argument #5 ($timeout) must be of type float, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($timeout) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $timeout = (double) $timeout;
            }
        }
        if (!extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use the FlowdockHandler');
        }
        parent::__construct('ssl://api.flowdock.com:443', $level, $bubble, $persistent, $timeout, $writingTimeout, $connectionTimeout, $chunkSize);
        $this->apiToken = $apiToken;
    }
    /**
     * {@inheritDoc}
     */
    public function setFormatter(FormatterInterface $formatter) : HandlerInterface
    {
        if (!$formatter instanceof FlowdockFormatter) {
            throw new \InvalidArgumentException('The FlowdockHandler requires an instance of Monolog\\Formatter\\FlowdockFormatter to function correctly');
        }
        return parent::setFormatter($formatter);
    }
    /**
     * Gets the default formatter.
     */
    protected function getDefaultFormatter() : FormatterInterface
    {
        throw new \InvalidArgumentException('The FlowdockHandler must be configured (via setFormatter) with an instance of Monolog\\Formatter\\FlowdockFormatter to function correctly');
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        parent::write($record);
        $this->closeSocket();
    }
    /**
     * {@inheritDoc}
     */
    protected function generateDataStream(array $record) : string
    {
        $content = $this->buildContent($record);
        return $this->buildHeader($content) . $content;
    }
    /**
     * Builds the body of API call
     *
     * @phpstan-param FormattedRecord $record
     */
    private function buildContent(array $record) : string
    {
        return Utils::jsonEncode($record['formatted']['flowdock']);
    }
    /**
     * Builds the header of the API Call
     */
    private function buildHeader(string $content) : string
    {
        $header = "POST /v1/messages/team_inbox/" . $this->apiToken . " HTTP/1.1\r\n";
        $header .= "Host: api.flowdock.com\r\n";
        $header .= "Content-Type: application/json\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";
        return $header;
    }
}