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

use Aws\Sqs\SqsClient;
use Monolog\Logger;
use Monolog\Utils;
/**
 * Writes to any sqs queue.
 *
 * @author Martijn van Calker <git@amvc.nl>
 */
class SqsHandler extends AbstractProcessingHandler
{
    /** 256 KB in bytes - maximum message size in SQS */
    protected const MAX_MESSAGE_SIZE = 262144;
    /** 100 KB in bytes - head message size for new error log */
    protected const HEAD_MESSAGE_SIZE = 102400;
    /** @var SqsClient */
    private $client;
    /** @var string */
    private $queueUrl;
    /**
     *
     * @param SqsClient $sqsClient
     * @param string $queueUrl
     */
    public function __construct($sqsClient, $queueUrl, $level = 100, bool $bubble = true)
    {
        if (!$sqsClient instanceof SqsClient) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($sqsClient) must be of type SqsClient, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($sqsClient) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!\is_string($queueUrl)) {
            if (!(\is_string($queueUrl) || \is_object($queueUrl) && \method_exists($queueUrl, '__toString') || (\is_bool($queueUrl) || \is_numeric($queueUrl)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($queueUrl) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($queueUrl) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $queueUrl = (string) $queueUrl;
            }
        }
        parent::__construct($level, $bubble);
        $this->client = $sqsClient;
        $this->queueUrl = $queueUrl;
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        if (!isset($record['formatted']) || 'string' !== gettype($record['formatted'])) {
            throw new \InvalidArgumentException('SqsHandler accepts only formatted records as a string' . Utils::getRecordMessageForException($record));
        }
        $messageBody = $record['formatted'];
        if (strlen($messageBody) >= static::MAX_MESSAGE_SIZE) {
            $messageBody = Utils::substr($messageBody, 0, static::HEAD_MESSAGE_SIZE);
        }
        $this->client->sendMessage(['QueueUrl' => $this->queueUrl, 'MessageBody' => $messageBody]);
    }
}