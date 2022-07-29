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
/**
 * Logs to syslog service.
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $syslog = new SyslogHandler('myfacility', 'local6');
 *   $formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
 *   $syslog->setFormatter($formatter);
 *   $log->pushHandler($syslog);
 *
 * @author Sven Paulus <sven@karlsruhe.org>
 */
class SyslogHandler extends AbstractSyslogHandler
{
    /** @var string */
    protected $ident;
    /** @var int */
    protected $logopts;
    /**
     * @param string $ident
     * @param (string | int) $facility Either one of the names of the keys in $this->facilities, or a LOG_* facility constant
     * @param int $logopts Option flags for the openlog() call, defaults to LOG_PID
     */
    public function __construct($ident, $facility = LOG_USER, $level = 100, bool $bubble = true, int $logopts = LOG_PID)
    {
        if (!\is_string($ident)) {
            if (!(\is_string($ident) || \is_object($ident) && \method_exists($ident, '__toString') || (\is_bool($ident) || \is_numeric($ident)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($ident) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($ident) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $ident = (string) $ident;
            }
        }
        parent::__construct($facility, $level, $bubble);
        $this->ident = $ident;
        $this->logopts = $logopts;
    }
    /**
     * {@inheritDoc}
     */
    public function close() : void
    {
        closelog();
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        if (!openlog($this->ident, $this->logopts, $this->facility)) {
            throw new \LogicException('Can\'t open syslog for ident "' . $this->ident . '" and facility "' . $this->facility . '"' . Utils::getRecordMessageForException($record));
        }
        syslog($this->logLevels[$record['level']], (string) $record['formatted']);
    }
}