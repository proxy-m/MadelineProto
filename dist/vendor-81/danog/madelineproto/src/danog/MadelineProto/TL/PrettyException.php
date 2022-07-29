<?php

/**
 * PrettyException module.
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
namespace danog\MadelineProto\TL;

/**
 * Handle async stack traces.
 */
trait PrettyException
{
    /**
     * TL trace.
     *
     * @var string
     */
    public $tlTrace = '';
    /**
     * Method name.
     *
     * @var string
     */
    private $method = '';
    /**
     * Whether the TL trace was updated.
     *
     * @var boolean
     */
    private $updated = false;
    /**
     * Update TL trace.
     *
     * @param array $trace
     *
     * @return void
     */
    public function updateTLTrace(array $trace)
    {
        if (!$this->updated) {
            $this->updated = true;
            $this->prettifyTL($this->method, $trace);
        }
    }
    /**
     * Get TL trace.
     *
     * @return string
     */
    public function getTLTrace() : string
    {
        return $this->tlTrace;
    }
    /**
     * Set TL trace.
     *
     * @param string $tlTrace TL trace
     *
     * @return void
     */
    public function setTLTrace(string $tlTrace) : void
    {
        $this->tlTrace = $tlTrace;
    }
    /**
     * Generate async trace.
     *
     * @param string $init  Method name
     * @param array  $trace Async trace
     *
     * @return void
     */
    public function prettifyTL(string $init = '', array $trace = null)
    {
        $this->method = $init;
        $previous_trace = $this->tlTrace;
        $this->tlTrace = '';
        $eol = PHP_EOL;
        if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') {
            $eol = '<br>' . PHP_EOL;
        }
        $tl = false;
        foreach (\array_reverse($trace ?? $this->getTrace()) as $k => $frame) {
            if (isset($frame['function']) && \in_array($frame['function'], ['serializeParams', 'serializeObject'])) {
                if (($frame['args'][2] ?? '') !== '') {
                    $this->tlTrace .= $tl ? "['" . $frame['args'][2] . "']" : "While serializing:  \t" . $frame['args'][2];
                    $tl = true;
                }
            } else {
                if ($tl) {
                    $this->tlTrace .= $eol;
                }
                if (isset($frame['function']) && ($frame['function'] === 'handle_rpc_error' && $k === \count($this->getTrace()) - 1) || $frame['function'] === 'unserialize') {
                    continue;
                }
                $this->tlTrace .= isset($frame['file']) ? \str_pad(\basename($frame['file']) . '(' . $frame['line'] . '):', 20) . "\t" : '';
                $this->tlTrace .= isset($frame['function']) ? $frame['function'] . '(' : '';
                $this->tlTrace .= isset($frame['args']) ? \substr(\json_encode($frame['args']), 1, -1) : '';
                $this->tlTrace .= ')';
                $this->tlTrace .= $eol;
                $tl = false;
            }
        }
        $this->tlTrace .= $init !== '' ? "['" . $init . "']" : '';
        $this->tlTrace = \implode($eol, \array_reverse(\explode($eol, $this->tlTrace)));
        if ($previous_trace) {
            $this->tlTrace .= $eol . $eol;
            $this->tlTrace .= "Previous TL trace:{$eol}";
            $this->tlTrace .= $previous_trace;
        }
    }
}