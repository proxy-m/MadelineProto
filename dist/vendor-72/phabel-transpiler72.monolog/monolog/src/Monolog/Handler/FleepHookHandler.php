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

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
/**
 * Sends logs to Fleep.io using Webhook integrations
 *
 * You'll need a Fleep.io account to use this handler.
 *
 * @see https://fleep.io/integrations/webhooks/ Fleep Webhooks Documentation
 * @author Ando Roots <ando@sqroot.eu>
 *
 * @phpstan-import-type FormattedRecord from AbstractProcessingHandler
 */
class FleepHookHandler extends SocketHandler
{
    protected const FLEEP_HOST = 'fleep.io';
    protected const FLEEP_HOOK_URI = '/hook/';
    /**
     * @var string Webhook token (specifies the conversation where logs are sent)
     */
    protected $token;
    /**
    * Construct a new Fleep.io Handler.
    *
    * For instructions on how to create a new web hook in your conversations
    see https://fleep.io/integrations/webhooks/
    *
    * @param string $token Webhook token
    * @throws MissingExtensionException
    * @param bool $bubble
    * @param float $timeout
    */
    public function __construct($token, $level = 100, $bubble = true, bool $persistent = false, $timeout = 0.0, float $writingTimeout = 10.0, ?float $connectionTimeout = NULL, ?int $chunkSize = NULL)
    {
        if (!\is_string($token)) {
            if (!(\is_string($token) || \is_object($token) && \method_exists($token, '__toString') || (\is_bool($token) || \is_numeric($token)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($token) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($token) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $token = (string) $token;
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
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use the FleepHookHandler');
        }
        $this->token = $token;
        $connectionString = 'ssl://' . static::FLEEP_HOST . ':443';
        parent::__construct($connectionString, $level, $bubble, $persistent, $timeout, $writingTimeout, $connectionTimeout, $chunkSize);
    }
    /**
     * Returns the default formatter to use with this handler
     *
     * Overloaded to remove empty context and extra arrays from the end of the log message.
     *
     * @return LineFormatter
     */
    protected function getDefaultFormatter() : FormatterInterface
    {
        return new LineFormatter(null, null, true, true);
    }
    /**
     * Handles a log record
     */
    public function write(array $record) : void
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
     * Builds the header of the API Call
     */
    private function buildHeader(string $content) : string
    {
        $header = "POST " . static::FLEEP_HOOK_URI . $this->token . " HTTP/1.1\r\n";
        $header .= "Host: " . static::FLEEP_HOST . "\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";
        return $header;
    }
    /**
     * Builds the body of API call
     *
     * @phpstan-param FormattedRecord $record
     */
    private function buildContent(array $record) : string
    {
        $dataArray = ['message' => $record['formatted']];
        return http_build_query($dataArray);
    }
}