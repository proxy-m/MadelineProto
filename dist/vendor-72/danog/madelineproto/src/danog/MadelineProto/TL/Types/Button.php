<?php

/**
 * Button module.
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
namespace danog\MadelineProto\TL\Types;

use danog\MadelineProto\API;
use danog\MadelineProto\Ipc\Client;
use danog\MadelineProto\MTProto;
use danog\MadelineProto\Tools;
/**
 * Clickable button.
 */
class Button implements \JsonSerializable, \ArrayAccess
{
    /**
     * Button data.
     *
     * @psalm-var array<array-key, mixed>
     */
    private $button = [];
    /**
     * Session name.
     * @var string $session
     */
    private $session = '';
    /**
     * MTProto instance.
     *
     * @var MTProto|Client|null
     */
    private $API = null;
    /**
     * Message ID.
     * @var int $id
     */
    private $id;
    /**
     * Peer ID.
     *
     * @var array|int
     */
    private $peer;
    /**
     * Constructor function.
     *
     * @param MTProto $API API instance
     * @param array $message Message
     * @param array $button Button info
     */
    public function __construct(MTProto $API, array $message, array $button)
    {
        if (!isset($message['from_id']) || $message['peer_id']['_'] !== 'peerUser' || $message['peer_id']['user_id'] !== $API->authorization['user']['id']) {
            $this->peer = $message['peer_id'];
        } else {
            $this->peer = $message['from_id'];
        }
        $this->button = $button;
        $this->id = $message['id'];
        $this->API = $API;
        $this->session = $API->getWrapper()->getSession()->getLegacySessionPath();
    }
    /**
     * Sleep function.
     *
     * @return array
     */
    public function __sleep() : array
    {
        return ['button', 'peer', 'id', 'session'];
    }
    /**
     * Click on button.
     *
     * @param boolean $donotwait Whether to wait for the result of the method
     *
     * @return mixed
     */
    public function click(bool $donotwait = true)
    {
        if (!isset($this->API)) {
            $this->API = Client::giveInstanceBySession($this->session);
        }
        $async = $this->API instanceof Client ? $this->API->async : $this->API->wrapper->isAsync();
        switch ($this->button['_']) {
            default:
                return false;
            case 'keyboardButtonUrl':
                return $this->button['url'];
            case 'keyboardButton':
                $res = $this->API->clickInternal($donotwait, 'messages.sendMessage', ['peer' => $this->peer, 'message' => $this->button['text'], 'reply_to_msg_id' => $this->id]);
                break;
            case 'keyboardButtonCallback':
                $res = $this->API->clickInternal($donotwait, 'messages.getBotCallbackAnswer', ['peer' => $this->peer, 'msg_id' => $this->id, 'data' => $this->button['data']]);
                break;
            case 'keyboardButtonGame':
                $res = $this->API->clickInternal($donotwait, 'messages.getBotCallbackAnswer', ['peer' => $this->peer, 'msg_id' => $this->id, 'game' => true]);
                break;
        }
        return $async ? $res : Tools::wait($res);
    }
    /**
     * Get debug info.
     *
     * @return array
     */
    public function __debugInfo() : array
    {
        $res = \get_object_vars($this);
        unset($res['API']);
        return $res;
    }
    /**
     * Serialize button.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->button;
    }
    /**
     * Set button info.
     *
     * @param mixed $name Offset
     * @param mixed $value Value
     *
     * @return void
     */
    public function offsetSet($name, $value) : void
    {
        if (!true) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($name) must be of type mixed, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($name) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!true) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($value) must be of type mixed, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($value) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if ($name === null) {
            $this->button[] = $value;
        } else {
            $this->button[$name] = $value;
        }
    }
    /**
     * Get button info.
     *
     * @param mixed $name Field name
     *
     * @return mixed
     */
    public function offsetGet($name)
    {
        if (!true) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($name) must be of type mixed, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($name) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        $phabelReturn = $this->button[$name];
        if (!true) {
            throw new \TypeError(__METHOD__ . '(): Return value must be of type mixed, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($phabelReturn) . ' returned in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        return $phabelReturn;
    }
    /**
     * Unset button info.
     *
     * @param mixed $name Offset
     *
     * @return void
     */
    public function offsetUnset($name) : void
    {
        if (!true) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($name) must be of type mixed, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($name) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        unset($this->button[$name]);
    }
    /**
     * Check if button field exists.
     *
     * @param mixed $name Offset
     *
     * @return boolean
     */
    public function offsetExists($name) : bool
    {
        if (!true) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($name) must be of type mixed, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($name) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        return isset($this->button[$name]);
    }
}