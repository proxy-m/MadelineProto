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

use Aws\Sdk;
use Aws\DynamoDb\DynamoDbClient;
use Monolog\Formatter\FormatterInterface;
use Aws\DynamoDb\Marshaler;
use Monolog\Formatter\ScalarFormatter;
use Monolog\Logger;
/**
 * Amazon DynamoDB handler (http://aws.amazon.com/dynamodb/)
 *
 * @link https://github.com/aws/aws-sdk-php/
 * @author Andrew Lawson <adlawson@gmail.com>
 */
class DynamoDbHandler extends AbstractProcessingHandler
{
    public const DATE_FORMAT = 'Y-m-d\\TH:i:s.uO';
    /**
     * @var DynamoDbClient
     */
    protected $client;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var int
     */
    protected $version;
    /**
     * @var Marshaler
     */
    protected $marshaler;
    /**
     *
     * @param DynamoDbClient $client
     * @param string $table
     */
    public function __construct($client, $table, $level = 100, bool $bubble = true)
    {
        if (!$client instanceof DynamoDbClient) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($client) must be of type DynamoDbClient, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($client) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!\is_string($table)) {
            if (!(\is_string($table) || \is_object($table) && \method_exists($table, '__toString') || (\is_bool($table) || \is_numeric($table)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($table) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($table) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $table = (string) $table;
            }
        }
        /** @phpstan-ignore-next-line */
        if (defined('Aws\\Sdk::VERSION') && version_compare(Sdk::VERSION, '3.0', '>=')) {
            $this->version = 3;
            $this->marshaler = new Marshaler();
        } else {
            $this->version = 2;
        }
        $this->client = $client;
        $this->table = $table;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $filtered = $this->filterEmptyFields($record['formatted']);
        if ($this->version === 3) {
            $formatted = $this->marshaler->marshalItem($filtered);
        } else {
            /** @phpstan-ignore-next-line */
            $formatted = $this->client->formatAttributes($filtered);
        }
        $this->client->putItem(['TableName' => $this->table, 'Item' => $formatted]);
    }
    /**
     * @param mixed[] $record
     * @return mixed[]
     */
    protected function filterEmptyFields(array $record) : array
    {
        return \Phabel\Target\Php74\Polyfill::array_filter($record, function ($value) {
            return !empty($value) || false === $value || 0 === $value;
        });
    }
    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter() : FormatterInterface
    {
        return new ScalarFormatter(self::DATE_FORMAT);
    }
}