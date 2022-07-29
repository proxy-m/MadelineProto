<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI;

use const PHP_EOL;
use function count;
use function explode;
use function max;
use function preg_replace_callback;
use function str_pad;
use function str_repeat;
use function strlen;
use function wordwrap;
use PHPUnit\Util\Color;
use SebastianBergmann\Environment\Console;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Help
{
    private const LEFT_MARGIN = '  ';
    private const HELP_TEXT = array('Usage' => array(0 => array('text' => 'phpunit [options] UnitTest.php'), 1 => array('text' => 'phpunit [options] <directory>')), 'Code Coverage Options' => array(0 => array('arg' => '--coverage-clover <file>', 'desc' => 'Generate code coverage report in Clover XML format'), 1 => array('arg' => '--coverage-cobertura <file>', 'desc' => 'Generate code coverage report in Cobertura XML format'), 2 => array('arg' => '--coverage-crap4j <file>', 'desc' => 'Generate code coverage report in Crap4J XML format'), 3 => array('arg' => '--coverage-html <dir>', 'desc' => 'Generate code coverage report in HTML format'), 4 => array('arg' => '--coverage-php <file>', 'desc' => 'Export PHP_CodeCoverage object to file'), 5 => array('arg' => '--coverage-text=<file>', 'desc' => 'Generate code coverage report in text format [default: standard output]'), 6 => array('arg' => '--coverage-xml <dir>', 'desc' => 'Generate code coverage report in PHPUnit XML format'), 7 => array('arg' => '--coverage-cache <dir>', 'desc' => 'Cache static analysis results'), 8 => array('arg' => '--warm-coverage-cache', 'desc' => 'Warm static analysis cache'), 9 => array('arg' => '--coverage-filter <dir>', 'desc' => 'Include <dir> in code coverage analysis'), 10 => array('arg' => '--path-coverage', 'desc' => 'Perform path coverage analysis'), 11 => array('arg' => '--disable-coverage-ignore', 'desc' => 'Disable annotations for ignoring code coverage'), 12 => array('arg' => '--no-coverage', 'desc' => 'Ignore code coverage configuration')), 'Logging Options' => array(0 => array('arg' => '--log-junit <file>', 'desc' => 'Log test execution in JUnit XML format to file'), 1 => array('arg' => '--log-teamcity <file>', 'desc' => 'Log test execution in TeamCity format to file'), 2 => array('arg' => '--testdox-html <file>', 'desc' => 'Write agile documentation in HTML format to file'), 3 => array('arg' => '--testdox-text <file>', 'desc' => 'Write agile documentation in Text format to file'), 4 => array('arg' => '--testdox-xml <file>', 'desc' => 'Write agile documentation in XML format to file'), 5 => array('arg' => '--reverse-list', 'desc' => 'Print defects in reverse order'), 6 => array('arg' => '--no-logging', 'desc' => 'Ignore logging configuration')), 'Test Selection Options' => array(0 => array('arg' => '--list-suites', 'desc' => 'List available test suites'), 1 => array('arg' => '--testsuite <name>', 'desc' => 'Filter which testsuite to run'), 2 => array('arg' => '--list-groups', 'desc' => 'List available test groups'), 3 => array('arg' => '--group <name>', 'desc' => 'Only runs tests from the specified group(s)'), 4 => array('arg' => '--exclude-group <name>', 'desc' => 'Exclude tests from the specified group(s)'), 5 => array('arg' => '--covers <name>', 'desc' => 'Only runs tests annotated with "@covers <name>"'), 6 => array('arg' => '--uses <name>', 'desc' => 'Only runs tests annotated with "@uses <name>"'), 7 => array('arg' => '--list-tests', 'desc' => 'List available tests'), 8 => array('arg' => '--list-tests-xml <file>', 'desc' => 'List available tests in XML format'), 9 => array('arg' => '--filter <pattern>', 'desc' => 'Filter which tests to run'), 10 => array('arg' => '--test-suffix <suffixes>', 'desc' => 'Only search for test in files with specified suffix(es). Default: Test.php,.phpt')), 'Test Execution Options' => array(0 => array('arg' => '--dont-report-useless-tests', 'desc' => 'Do not report tests that do not test anything'), 1 => array('arg' => '--strict-coverage', 'desc' => 'Be strict about @covers annotation usage'), 2 => array('arg' => '--strict-global-state', 'desc' => 'Be strict about changes to global state'), 3 => array('arg' => '--disallow-test-output', 'desc' => 'Be strict about output during tests'), 4 => array('arg' => '--disallow-resource-usage', 'desc' => 'Be strict about resource usage during small tests'), 5 => array('arg' => '--enforce-time-limit', 'desc' => 'Enforce time limit based on test size'), 6 => array('arg' => '--default-time-limit <sec>', 'desc' => 'Timeout in seconds for tests without @small, @medium or @large'), 7 => array('arg' => '--disallow-todo-tests', 'desc' => 'Disallow @todo-annotated tests'), 8 => array('spacer' => ''), 9 => array('arg' => '--process-isolation', 'desc' => 'Run each test in a separate PHP process'), 10 => array('arg' => '--globals-backup', 'desc' => 'Backup and restore $GLOBALS for each test'), 11 => array('arg' => '--static-backup', 'desc' => 'Backup and restore static attributes for each test'), 12 => array('spacer' => ''), 13 => array('arg' => '--colors <flag>', 'desc' => 'Use colors in output ("never", "auto" or "always")'), 14 => array('arg' => '--columns <n>', 'desc' => 'Number of columns to use for progress output'), 15 => array('arg' => '--columns max', 'desc' => 'Use maximum number of columns for progress output'), 16 => array('arg' => '--stderr', 'desc' => 'Write to STDERR instead of STDOUT'), 17 => array('arg' => '--stop-on-defect', 'desc' => 'Stop execution upon first not-passed test'), 18 => array('arg' => '--stop-on-error', 'desc' => 'Stop execution upon first error'), 19 => array('arg' => '--stop-on-failure', 'desc' => 'Stop execution upon first error or failure'), 20 => array('arg' => '--stop-on-warning', 'desc' => 'Stop execution upon first warning'), 21 => array('arg' => '--stop-on-risky', 'desc' => 'Stop execution upon first risky test'), 22 => array('arg' => '--stop-on-skipped', 'desc' => 'Stop execution upon first skipped test'), 23 => array('arg' => '--stop-on-incomplete', 'desc' => 'Stop execution upon first incomplete test'), 24 => array('arg' => '--fail-on-incomplete', 'desc' => 'Treat incomplete tests as failures'), 25 => array('arg' => '--fail-on-risky', 'desc' => 'Treat risky tests as failures'), 26 => array('arg' => '--fail-on-skipped', 'desc' => 'Treat skipped tests as failures'), 27 => array('arg' => '--fail-on-warning', 'desc' => 'Treat tests with warnings as failures'), 28 => array('arg' => '-v|--verbose', 'desc' => 'Output more verbose information'), 29 => array('arg' => '--debug', 'desc' => 'Display debugging information'), 30 => array('spacer' => ''), 31 => array('arg' => '--repeat <times>', 'desc' => 'Runs the test(s) repeatedly'), 32 => array('arg' => '--teamcity', 'desc' => 'Report test execution progress in TeamCity format'), 33 => array('arg' => '--testdox', 'desc' => 'Report test execution progress in TestDox format'), 34 => array('arg' => '--testdox-group', 'desc' => 'Only include tests from the specified group(s)'), 35 => array('arg' => '--testdox-exclude-group', 'desc' => 'Exclude tests from the specified group(s)'), 36 => array('arg' => '--no-interaction', 'desc' => 'Disable TestDox progress animation'), 37 => array('arg' => '--printer <printer>', 'desc' => 'TestListener implementation to use'), 38 => array('spacer' => ''), 39 => array('arg' => '--order-by <order>', 'desc' => 'Run tests in order: default|defects|duration|no-depends|random|reverse|size'), 40 => array('arg' => '--random-order-seed <N>', 'desc' => 'Use a specific random seed <N> for random order'), 41 => array('arg' => '--cache-result', 'desc' => 'Write test results to cache file'), 42 => array('arg' => '--do-not-cache-result', 'desc' => 'Do not write test results to cache file')), 'Configuration Options' => array(0 => array('arg' => '--prepend <file>', 'desc' => 'A PHP script that is included as early as possible'), 1 => array('arg' => '--bootstrap <file>', 'desc' => 'A PHP script that is included before the tests run'), 2 => array('arg' => '-c|--configuration <file>', 'desc' => 'Read configuration from XML file'), 3 => array('arg' => '--no-configuration', 'desc' => 'Ignore default configuration file (phpunit.xml)'), 4 => array('arg' => '--extensions <extensions>', 'desc' => 'A comma separated list of PHPUnit extensions to load'), 5 => array('arg' => '--no-extensions', 'desc' => 'Do not load PHPUnit extensions'), 6 => array('arg' => '--include-path <path(s)>', 'desc' => 'Prepend PHP\'s include_path with given path(s)'), 7 => array('arg' => '-d <key[=value]>', 'desc' => 'Sets a php.ini value'), 8 => array('arg' => '--cache-result-file <file>', 'desc' => 'Specify result cache path and filename'), 9 => array('arg' => '--generate-configuration', 'desc' => 'Generate configuration file with suggested settings'), 10 => array('arg' => '--migrate-configuration', 'desc' => 'Migrate configuration file to current format')), 'Miscellaneous Options' => array(0 => array('arg' => '-h|--help', 'desc' => 'Prints this usage information'), 1 => array('arg' => '--version', 'desc' => 'Prints the version and exits'), 2 => array('arg' => '--atleast-version <min>', 'desc' => 'Checks that version is greater than min and exits'), 3 => array('arg' => '--check-version', 'desc' => 'Check whether PHPUnit is the latest version')));
    /**
     * @var int Number of columns required to write the longest option name to the console
     */
    private $maxArgLength = 0;
    /**
     * @var int Number of columns left for the description field after padding and option
     */
    private $maxDescLength;
    /**
     * @var bool Use color highlights for sections, options and parameters
     */
    private $hasColor = false;
    public function __construct(?int $width = NULL, ?bool $withColor = NULL)
    {
        if ($width === null) {
            $width = (new Console())->getNumberOfColumns();
        }
        if ($withColor === null) {
            $this->hasColor = (new Console())->hasColorSupport();
        } else {
            $this->hasColor = $withColor;
        }
        foreach (self::HELP_TEXT as $options) {
            foreach ($options as $option) {
                if (isset($option['arg'])) {
                    $this->maxArgLength = max($this->maxArgLength, isset($option['arg']) ? strlen($option['arg']) : 0);
                }
            }
        }
        $this->maxDescLength = $width - $this->maxArgLength - 4;
    }
    /**
     * Write the help file to the CLI, adapting width and colors to the console.
     */
    public function writeToConsole() : void
    {
        if ($this->hasColor) {
            $this->writeWithColor();
        } else {
            $this->writePlaintext();
        }
    }
    private function writePlaintext() : void
    {
        foreach (self::HELP_TEXT as $section => $options) {
            print "{$section}:" . PHP_EOL;
            if ($section !== 'Usage') {
                print PHP_EOL;
            }
            foreach ($options as $option) {
                if (isset($option['spacer'])) {
                    print PHP_EOL;
                }
                if (isset($option['text'])) {
                    print self::LEFT_MARGIN . $option['text'] . PHP_EOL;
                }
                if (isset($option['arg'])) {
                    $arg = str_pad($option['arg'], $this->maxArgLength);
                    print self::LEFT_MARGIN . $arg . ' ' . $option['desc'] . PHP_EOL;
                }
            }
            print PHP_EOL;
        }
    }
    private function writeWithColor() : void
    {
        foreach (self::HELP_TEXT as $section => $options) {
            print Color::colorize('fg-yellow', "{$section}:") . PHP_EOL;
            foreach ($options as $option) {
                if (isset($option['spacer'])) {
                    print PHP_EOL;
                }
                if (isset($option['text'])) {
                    print self::LEFT_MARGIN . $option['text'] . PHP_EOL;
                }
                if (isset($option['arg'])) {
                    $arg = Color::colorize('fg-green', str_pad($option['arg'], $this->maxArgLength));
                    $arg = preg_replace_callback('/(<[^>]+>)/', static function ($matches) {
                        return Color::colorize('fg-cyan', $matches[0]);
                    }, $arg);
                    $desc = explode(PHP_EOL, wordwrap($option['desc'], $this->maxDescLength, PHP_EOL));
                    print self::LEFT_MARGIN . $arg . ' ' . $desc[0] . PHP_EOL;
                    for ($i = 1; $i < count($desc); $i++) {
                        print str_repeat(' ', $this->maxArgLength + 3) . $desc[$i] . PHP_EOL;
                    }
                }
            }
            print PHP_EOL;
        }
    }
}