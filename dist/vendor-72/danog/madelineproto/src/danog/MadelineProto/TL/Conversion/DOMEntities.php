<?php

namespace danog\MadelineProto\TL\Conversion;

use danog\MadelineProto\StrTools;
use DOMNode;
use DOMText;
final class DOMEntities
{
    /**
     * @readonly
     * @var array $entities
     */
    public $entities = [];
    /**
     * @readonly
     * @var array $buttons
     */
    public $buttons = [];
    /**
     * @readonly
     * @var string $message
     */
    public $message = '';
    /**
     *
     */
    public function __construct(string $html)
    {
        $dom = new \DOMDocument();
        $html = \preg_replace("/\\<br(\\s*)?\\/?\\>/i", "\n", $html);
        $dom->loadxml("<body>" . \str_replace(['&amp;', '&#039;', '&quot;', '&'], ['&', '\'', "\"", '&amp;'], \trim($html)) . "</body>");
        $this->parseNode($dom->getElementsByTagName('body')->item(0), 0);
    }
    /**
     * @return integer Length of the node
     * @param (DOMNode | DOMText) $node
     */
    private function parseNode($node, int $offset) : int
    {
        if (!($node instanceof DOMNode || $node instanceof DOMText)) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($node) must be of type DOMNode|DOMText, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($node) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if ($node instanceof DOMText) {
            $this->message .= $node->wholeText;
            return StrTools::mbStrlen($node->wholeText);
        }
        if ($node->nodeName === 'br') {
            $this->message .= "\n";
            return 1;
        }
        $entity = (function ($phabel_35a9cd686b87585d) use(&$node) {
            if ($phabel_35a9cd686b87585d === 's') {
                return ['_' => 'messageEntityStrike'];
            } elseif ($phabel_35a9cd686b87585d === 'strike') {
                return ['_' => 'messageEntityStrike'];
            } elseif ($phabel_35a9cd686b87585d === 'del') {
                return ['_' => 'messageEntityStrike'];
            } elseif ($phabel_35a9cd686b87585d === 'u') {
                return ['_' => 'messageEntityUnderline'];
            } elseif ($phabel_35a9cd686b87585d === 'blockquote') {
                return ['_' => 'messageEntityBlockquote'];
            } elseif ($phabel_35a9cd686b87585d === 'b') {
                return ['_' => 'messageEntityBold'];
            } elseif ($phabel_35a9cd686b87585d === 'strong') {
                return ['_' => 'messageEntityBold'];
            } elseif ($phabel_35a9cd686b87585d === 'i') {
                return ['_' => 'messageEntityItalic'];
            } elseif ($phabel_35a9cd686b87585d === 'em') {
                return ['_' => 'messageEntityItalic'];
            } elseif ($phabel_35a9cd686b87585d === 'code') {
                return ['_' => 'messageEntityCode'];
            } elseif ($phabel_35a9cd686b87585d === 'spoiler') {
                return ['_' => 'messageEntitySpoiler'];
            } elseif ($phabel_35a9cd686b87585d === 'tg-spoiler') {
                return ['_' => 'messageEntitySpoiler'];
            } elseif ($phabel_35a9cd686b87585d === 'pre') {
                return ['_' => 'messageEntityPre', 'language' => $node->getAttribute('language') ?? ''];
            } elseif ($phabel_35a9cd686b87585d === 'a') {
                return $this->handleA($node);
            } else {
                return null;
            }
        })($node->nodeName);
        $length = 0;
        foreach ($node->childNodes as $sub) {
            $length += $this->parseNode($sub, $offset + $length);
        }
        if ($entity !== null) {
            $lengthReal = $length;
            for ($x = \strlen($this->message) - 1; $x >= 0; $x--) {
                if (!($this->message[$x] === ' ' || $this->message[$x] === "\r" || $this->message[$x] === "\n")) {
                    break;
                }
                $lengthReal--;
            }
            if ($lengthReal > 0) {
                $entity['offset'] = $offset;
                $entity['length'] = $lengthReal;
                $this->entities[] = $entity;
            }
        }
        return $length;
    }
    /**
     *
     */
    private function handleA(DOMNode $node) : array
    {
        $href = $node->getAttribute('href');
        if (\preg_match('|mention:(.*)|', $href, $matches) || \preg_match('|tg://user\\?id=(.*)|', $href, $matches)) {
            return ['_' => 'inputMessageEntityMentionName', 'user_id' => $matches[1]];
        }
        if (\preg_match('|buttonurl:(.*)|', $href)) {
            if (\strpos(\Phabel\Target\Php80\Polyfill::substr($href, -4), '|:new|') !== false) {
                $this->buttons[] = ['_' => 'keyboardButtonUrl', 'text' => $text, 'url' => \str_replace(['buttonurl:', ':new'], '', $href), 'new' => true];
            } else {
                $this->buttons[] = ['_' => 'keyboardButtonUrl', 'text' => $text, 'url' => \str_replace('buttonurl:', '', $href)];
            }
            return null;
        }
        return ['_' => 'messageEntityTextUrl', 'url' => $href];
    }
}