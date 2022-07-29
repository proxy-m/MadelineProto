<?php

namespace Amp\Log;

use Monolog\Formatter\LineFormatter;
use Psr\Log\LogLevel;
final class ConsoleFormatter extends LineFormatter
{
    const DEFAULT_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\r\n";
    /** @var bool */
    private $colors;
    public function __construct(string $format = null, string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false)
    {
        parent::__construct($format ?? self::DEFAULT_FORMAT, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
        $this->setAnsiColorOption();
    }
    public function format(array $record) : string
    {
        if ($this->colors) {
            $record['level_name'] = $this->ansifyLevel($record['level_name']);
            $record['channel'] = "\x1b[1m{$record['channel']}\x1b[0m";
        }
        return parent::format($record);
    }
    private function setAnsiColorOption() : void
    {
        $value = \getenv("AMP_LOG_COLOR");
        if ($value === false || $value === '') {
            $value = "auto";
        }
        $value = \strtolower($value);
        switch ($value) {
            case "1":
            case "true":
            case "on":
                $this->colors = true;
                break;
            case "0":
            case "false":
            case "off":
                $this->colors = false;
                break;
            default:
                $this->colors = hasColorSupport();
                break;
        }
    }
    private function ansifyLevel(string $level) : string
    {
        $level = \strtolower($level);
        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
                return "\x1b[1;31m{$level}\x1b[0m";
            // bold + red
            case LogLevel::WARNING:
                return "\x1b[1;33m{$level}\x1b[0m";
            // bold + yellow
            case LogLevel::NOTICE:
                return "\x1b[1;32m{$level}\x1b[0m";
            // bold + green
            case LogLevel::INFO:
                return "\x1b[1;35m{$level}\x1b[0m";
            // bold + magenta
            case LogLevel::DEBUG:
                return "\x1b[1;36m{$level}\x1b[0m";
            // bold + cyan
            default:
                return "\x1b[1m{$level}\x1b[0m";
        }
    }
}