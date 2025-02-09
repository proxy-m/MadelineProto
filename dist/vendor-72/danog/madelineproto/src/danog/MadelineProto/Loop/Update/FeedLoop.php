<?php

/**
 * Update feeder loop.
 *
 * This file is part of MadelineProto.
 * MadelineProto is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * MadelineProto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU General Public License along with MadelineProto.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Daniil Gentili <daniil@daniil.it>
 * @copyright 2016-2020 Daniil Gentili <daniil@daniil.it>
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPLv3
 *
 * @link https://docs.madelineproto.xyz MadelineProto documentation
 */
namespace danog\MadelineProto\Loop\Update;

use danog\Loop\ResumableSignalLoop;
use danog\MadelineProto\Loop\InternalLoop;
use danog\MadelineProto\MTProto;
use danog\MadelineProto\MTProtoTools\UpdatesState;
/**
 * Update feed loop.
 *
 * @author Daniil Gentili <daniil@daniil.it>
 */
class FeedLoop extends ResumableSignalLoop
{
    use InternalLoop {
        __construct as private init;
    }
    /**
     * Main loop ID.
     */
    const GENERIC = 0;
    /**
     * Incoming updates array.
     * @var array $incomingUpdates
     */
    private $incomingUpdates = [];
    /**
     * Parsed updates array.
     * @var array $parsedUpdates
     */
    private $parsedUpdates = [];
    /**
     * Channel ID.
     * @var int $channelId
     */
    private $channelId;
    /**
     * Update loop.
     *
     * @var UpdateLoop
     */
    private $updater = null;
    /**
     * Update state.
     * @var (UpdatesState | null) $state
     */
    private $state = null;
    /**
     * Constructor.
     *
     * @param MTProto $API API instance
     * @param integer $channelId Constructor
     */
    public function __construct(MTProto $API, int $channelId = 0)
    {
        $this->init($API);
        $this->channelId = $channelId;
    }
    /**
     * Main loop.
     *
     * @return \Generator
     */
    public function loop() : \Generator
    {
        $API = $this->API;
        $this->updater = $API->updaters[$this->channelId];
        if (yield from $this->waitForAuthOrSignal()) {
            return;
        }
        $this->state = $this->channelId === self::GENERIC ? yield from $API->loadUpdateState() : $API->loadChannelState($this->channelId);
        while (true) {
            $API->logger->logger("Resumed {$this}");
            while ($this->incomingUpdates) {
                $updates = $this->incomingUpdates;
                $this->incomingUpdates = [];
                yield from $this->parse($updates);
                $updates = null;
            }
            while ($this->parsedUpdates) {
                $parsedUpdates = $this->parsedUpdates;
                $this->parsedUpdates = [];
                foreach ($parsedUpdates as $update) {
                    yield from $API->saveUpdate($update);
                }
                $parsedUpdates = null;
                $this->API->signalUpdate();
            }
            if (yield from $this->waitForAuthOrSignal()) {
                return;
            }
        }
    }
    /**
     *
     */
    public function parse(array $updates) : \Generator
    {
        \reset($updates);
        while ($updates) {
            $key = \key($updates);
            $update = $updates[$key];
            unset($updates[$key]);
            if ($update['_'] === 'updateChannelTooLong') {
                $this->API->logger->logger('Got channel too long update, getting difference...', \danog\MadelineProto\Logger::VERBOSE);
                (yield $this->updater->resume());
                continue;
            }
            if (isset($update['pts'], $update['pts_count'])) {
                $logger = function ($msg) use($update) : void {
                    $pts_count = $update['pts_count'];
                    $mid = isset($update['message']['id']) ? $update['message']['id'] : '-';
                    $mypts = $this->state->pts();
                    $computed = $mypts + $pts_count;
                    $this->API->logger->logger("{$msg}. My pts: {$mypts}, remote pts: {$update['pts']}, computed pts: {$computed}, msg id: {$mid}, channel id: {$this->channelId}", \danog\MadelineProto\Logger::ULTRA_VERBOSE);
                };
                $result = $this->state->checkPts($update);
                if ($result < 0) {
                    $logger('PTS duplicate');
                    continue;
                }
                if ($result > 0) {
                    $logger('PTS hole');
                    $this->updater->setLimit($this->state->pts() + $result);
                    (yield $this->updater->resume());
                    $updates = \array_merge($this->incomingUpdates, $updates);
                    $this->incomingUpdates = [];
                    continue;
                }
                if (isset($update['message']['id'], $update['message']['peer_id']) && !\in_array($update['_'], ['updateEditMessage', 'updateEditChannelMessage', 'updateMessageID'])) {
                    if (!$this->API->checkMsgId($update['message'])) {
                        $logger('MSGID duplicate');
                        continue;
                    }
                }
                $logger('PTS OK');
                $this->state->pts($update['pts']);
            }
            $this->save($update);
        }
    }
    /**
     *
     */
    public function feed(array $updates) : \Generator
    {
        $result = [];
        foreach ($updates as $update) {
            $result[yield from $this->feedSingle($update)] = true;
        }
        return $result;
    }
    /**
     *
     */
    public function feedSingle(array $update) : \Generator
    {
        $channelId = self::GENERIC;
        switch ($update['_']) {
            case 'updateNewChannelMessage':
            case 'updateEditChannelMessage':
                $channelId = isset($update['message']['peer_id']['channel_id']) ? $update['message']['peer_id']['channel_id'] : self::GENERIC;
                if (!$channelId) {
                    return false;
                }
                break;
            case 'updateChannelWebPage':
            case 'updateDeleteChannelMessages':
                $channelId = $update['channel_id'];
                break;
            case 'updateChannelTooLong':
                $channelId = isset($update['channel_id']) ? $update['channel_id'] : self::GENERIC;
                if (!isset($update['pts'])) {
                    $update['pts'] = 1;
                }
                break;
        }
        if ($channelId && !$this->API->getChannelStates()->has($channelId)) {
            $this->API->loadChannelState($channelId, $update);
            if (!isset($this->API->feeders[$channelId])) {
                $this->API->feeders[$channelId] = new self($this->API, $channelId);
            }
            if (!isset($this->API->updaters[$channelId])) {
                $this->API->updaters[$channelId] = new UpdateLoop($this->API, $channelId);
            }
            $this->API->feeders[$channelId]->start();
            $this->API->updaters[$channelId]->start();
        }
        switch ($update['_']) {
            case 'updateNewMessage':
            case 'updateEditMessage':
            case 'updateNewChannelMessage':
            case 'updateEditChannelMessage':
                $to = false;
                $from = false;
                $via_bot = false;
                $entities = false;
                if ($update['message']['_'] !== 'messageEmpty' && (($from = isset($update['message']['from_id']) && !(yield from $this->API->peerIsset($update['message']['from_id']))) || ($to = !(yield from $this->API->peerIsset($update['message']['peer_id']))) || ($via_bot = isset($update['message']['via_bot_id']) && !(yield from $this->API->peerIsset($update['message']['via_bot_id']))) || ($entities = isset($update['message']['entities']) && !(yield from $this->API->entitiesPeerIsset($update['message']['entities']))))) {
                    $log = '';
                    if ($from) {
                        $from_id = $this->API->getId($update['message']['from_id']);
                        $log .= "from_id {$from_id}, ";
                    }
                    if ($to) {
                        $log .= 'peer_id ' . \json_encode($update['message']['peer_id']) . ', ';
                    }
                    if ($via_bot) {
                        $log .= "via_bot {$update['message']['via_bot_id']}, ";
                    }
                    if ($entities) {
                        $log .= 'entities ' . \json_encode($update['message']['entities']) . ', ';
                    }
                    $this->API->logger->logger("Not enough data: for message update {$log}, getting difference...", \danog\MadelineProto\Logger::VERBOSE);
                    $update = ['_' => 'updateChannelTooLong'];
                    if ($channelId && $to) {
                        $channelId = self::GENERIC;
                    }
                }
                break;
            default:
                if ($channelId && !(yield from $this->API->peerIsset($this->API->toSupergroup($channelId)))) {
                    $this->API->logger->logger('Skipping update, I do not have the channel id ' . $channelId, \danog\MadelineProto\Logger::ERROR);
                    return false;
                }
                break;
        }
        if ($channelId !== $this->channelId) {
            if (isset($this->API->feeders[$channelId])) {
                return yield from $this->API->feeders[$channelId]->feedSingle($update);
            } elseif ($this->channelId) {
                return yield from $this->API->feeders[self::GENERIC]->feedSingle($update);
            }
        }
        $this->API->logger->logger('Was fed an update of type ' . $update['_'] . " in {$this}...", \danog\MadelineProto\Logger::ULTRA_VERBOSE);
        $this->incomingUpdates[] = $update;
        return $this->channelId;
    }
    /**
     *
     */
    public function save($update) : void
    {
        $this->parsedUpdates[] = $update;
    }
    /**
     *
     */
    public function saveMessages($messages) : void
    {
        foreach ($messages as $message) {
            if (!$this->API->checkMsgId($message)) {
                $this->API->logger->logger("MSGID duplicate ({$message['id']}) in {$this}");
                continue;
            }
            if ($message['_'] !== 'messageEmpty') {
                $this->API->logger->logger('Getdiff fed me message of type ' . $message['_'] . " in {$this}...", \danog\MadelineProto\Logger::VERBOSE);
            }
            $this->parsedUpdates[] = ['_' => $this->channelId === self::GENERIC ? 'updateNewMessage' : 'updateNewChannelMessage', 'message' => $message, 'pts' => -1, 'pts_count' => -1];
        }
    }
    /**
     *
     */
    public function __toString() : string
    {
        return !$this->channelId ? 'update feed loop generic' : "update feed loop channel {$this->channelId}";
    }
}