<?php

if (\defined('MADELINE_PHP')) {
    return;
}

if (\class_exists(\Composer\Autoload\ClassLoader::class)) {
    throw new \Exception('Please require MadelineProto using composer.');
}

\define('MADELINE_PHP', __FILE__);

function ___install_madeline()
{
    if (\count(\debug_backtrace(0)) === 1) {
        if (isset($GLOBALS['argv']) && !empty($GLOBALS['argv'])) {
            $arguments = \array_slice($GLOBALS['argv'], 1);
        } elseif (isset($_GET['argv']) && !empty($_GET['argv'])) {
            $arguments = $_GET['argv'];
        } else {
            $arguments = [];
        }
        if (\count($arguments) >= 2) {
            \define(\MADELINE_WORKER_TYPE::class, \array_shift($arguments));
            \define(\MADELINE_WORKER_ARGS::class, $arguments);
        } else {
            die('MadelineProto loader: you must include this file in another PHP script, see https://docs.madelineproto.xyz for more info.'.PHP_EOL);
        }
    }
    if ((PHP_MAJOR_VERSION === 7 && PHP_MINOR_VERSION < 1) || PHP_MAJOR_VERSION < 7) {
        throw new \Exception('MadelineProto requires at least PHP 7.1 to run');
    }
    if (PHP_INT_SIZE < 8) {
        throw new \Exception('A 64-bit build of PHP is required to run MadelineProto, PHP 8.0+ recommended.');
    }

    // Template strings for madelineProto update URLs
    $release_template = 'https://phar.madelineproto.xyz/release%s?v=new';
    $phar_template = 'https://phar.madelineproto.xyz/madeline%s.phar?v=new';

    // Version definition
    $version = (string) \min(80, (int) (PHP_MAJOR_VERSION.PHP_MINOR_VERSION));
    $versions = [$version];

    // Checking if defined branch/default branch builds can be downloaded
    foreach ($versions as $chosen) {
        if ($release = @\file_get_contents(\sprintf($release_template, $chosen))) {
            break;
        }
    }
    if (!$release) {
        return;
    }

    $madeline_phar = "madeline-$version.phar";
    \define('HAD_MADELINE_PHAR', \file_exists($madeline_phar));

    if (!\file_exists($madeline_phar) || !\file_exists("$madeline_phar.version") || \file_get_contents("$madeline_phar.version") !== $release) {
        $phar = \file_get_contents(\sprintf($phar_template, $chosen));

        if ($phar) {
            $extractVersions = static function ($ext = '') use ($madeline_phar) {
                if (!\file_exists("phar://$madeline_phar$ext/vendor/composer/installed.json")) {
                    return [];
                }
                $composer = \json_decode(\file_get_contents("phar://$madeline_phar$ext/vendor/composer/installed.json"), true) ?: [];
                if (!isset($composer['packages'])) {
                    return [];
                }
                $packages = [];
                foreach ($composer['packages'] as $dep) {
                    $name = $dep['name'];
                    if (\strpos($name, 'phabel/transpiler') === 0) {
                        $name = \explode('/', $name, 3)[2];
                    }
                    $packages[$name] = $dep['version_normalized'];
                }
                return $packages;
            };
            $previous = [];
            if (\file_exists($madeline_phar)) {
                \copy($madeline_phar, "$madeline_phar.old");
                $previous = $extractVersions('.old');
                \unlink("$madeline_phar.old");
            }
            $previous['danog/madelineproto'] = 'old';

            \file_put_contents($madeline_phar, $phar, LOCK_EX);
            \file_put_contents("$madeline_phar.version", $release, LOCK_EX);
            unset($phar);

            $current = $extractVersions();
            $postData = ['downloads' => []];
            foreach ($current as $name => $version) {
                if (isset($previous[$name]) && $previous[$name] === $version) {
                    continue;
                }
                $postData['downloads'][] = [
                    'name' => $name,
                    'version' => $version
                ];
            }

            if (\defined('HHVM_VERSION')) {
                $phpVersion = 'HHVM '.HHVM_VERSION;
            } else {
                $phpVersion = 'PHP '.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION.'.'.PHP_RELEASE_VERSION;
            }
            $opts = ['http' =>
                [
                    'method' => 'POST',
                    'header' => [
                        'Content-Type: application/json',
                        \sprintf(
                            'User-Agent: Composer/%s (%s; %s; %s; %s%s)',
                            'Madeline v7',
                            \function_exists('php_uname') ? @\php_uname('s') : 'Unknown',
                            \function_exists('php_uname') ? @\php_uname('r') : 'Unknown',
                            $phpVersion,
                            'streams',
                            \getenv('CI') ? '; CI' : ''
                        )
                     ],
                    'content' => \json_encode($postData),
                    'timeout' => 6,
                ],
            ];
            @\file_get_contents("https://packagist.org/downloads/", false, \stream_context_create($opts));
        }
    }

    $result = require_once $madeline_phar;
    if (\defined('MADELINE_WORKER_TYPE') && \constant('MADELINE_WORKER_TYPE') === 'madeline-ipc') {
        require_once "phar://$madeline_phar/vendor/danog/madelineproto/src/danog/MadelineProto/Ipc/Runner/entry.php";
    }
    return $result;
}

return ___install_madeline();
