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
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\FormatterInterface;
use Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends AbstractProcessingHandler
{
    /** @var CouchDBClient */
    private $client;
    /**
     *
     * @param CouchDBClient $client
     */
    public function __construct($client, $level = 100, bool $bubble = true)
    {
        if (!$client instanceof CouchDBClient) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($client) must be of type CouchDBClient, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($client) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->client->postDocument($record['formatted']);
    }
    /**
     *
     */
    protected function getDefaultFormatter() : FormatterInterface
    {
        return new NormalizerFormatter();
    }
}