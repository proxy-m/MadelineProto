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
use Monolog\Logger;
use Monolog\Utils;
use Monolog\Handler\Slack\SlackRecord;
/**
 * Sends notifications through Slack Webhooks
 *
 * @author Haralan Dobrev <hkdobrev@gmail.com>
 * @see    https://api.slack.com/incoming-webhooks
 */
class SlackWebhookHandler extends AbstractProcessingHandler
{
    /**
     * Slack Webhook token
     * @var string
     */
    private $webhookUrl;
    /**
     * Instance of the SlackRecord util class preparing data for Slack API.
     * @var SlackRecord
     */
    private $slackRecord;
    /**
     * @param string $webhookUrl Slack Webhook URL
     * @param (string | null) $channel Slack channel (encoded ID or name)
     * @param (string | null) $username Name of a bot
     * @param bool $useAttachment Whether the message should be added to Slack as attachment (plain text otherwise)
     * @param (string | null) $iconEmoji The emoji name to use (or null)
     * @param bool $useShortAttachment Whether the the context/extra messages added to Slack as attachments are in a short style
     * @param bool $includeContextAndExtra Whether the attachment should include context and extra data
     * @param string[] $excludeFields Dot separated list of fields to exclude from slack message. E.g. ['context.field1', 'extra.field2']
     */
    public function __construct($webhookUrl, $channel = NULL, ?string $username = NULL, bool $useAttachment = true, ?string $iconEmoji = NULL, bool $useShortAttachment = false, bool $includeContextAndExtra = false, $level = 500, bool $bubble = true, array $excludeFields = array())
    {
        if (!\is_string($webhookUrl)) {
            if (!(\is_string($webhookUrl) || \is_object($webhookUrl) && \method_exists($webhookUrl, '__toString') || (\is_bool($webhookUrl) || \is_numeric($webhookUrl)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($webhookUrl) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($webhookUrl) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $webhookUrl = (string) $webhookUrl;
            }
        }
        if (!(\is_string($channel) || \is_null($channel))) {
            if (!(\is_string($channel) || \is_object($channel) && \method_exists($channel, '__toString') || (\is_bool($channel) || \is_numeric($channel)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($channel) must be of type ?string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($channel) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $channel = (string) $channel;
            }
        }
        if (!extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is needed to use the SlackWebhookHandler');
        }
        parent::__construct($level, $bubble);
        $this->webhookUrl = $webhookUrl;
        $this->slackRecord = new SlackRecord($channel, $username, $useAttachment, $iconEmoji, $useShortAttachment, $includeContextAndExtra, $excludeFields);
    }
    /**
     *
     */
    public function getSlackRecord() : SlackRecord
    {
        return $this->slackRecord;
    }
    /**
     *
     */
    public function getWebhookUrl() : string
    {
        return $this->webhookUrl;
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $postData = $this->slackRecord->getSlackData($record);
        $postString = Utils::jsonEncode($postData);
        $ch = curl_init();
        $options = array(CURLOPT_URL => $this->webhookUrl, CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => array('Content-type: application/json'), CURLOPT_POSTFIELDS => $postString);
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            $options[CURLOPT_SAFE_UPLOAD] = true;
        }
        curl_setopt_array($ch, $options);
        Curl\Util::execute($ch);
    }
    /**
     *
     */
    public function setFormatter(FormatterInterface $formatter) : HandlerInterface
    {
        parent::setFormatter($formatter);
        $this->slackRecord->setFormatter($formatter);
        return $this;
    }
    /**
     *
     */
    public function getFormatter() : FormatterInterface
    {
        $formatter = parent::getFormatter();
        $this->slackRecord->setFormatter($formatter);
        return $formatter;
    }
}