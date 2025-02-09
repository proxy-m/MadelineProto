<?php

namespace Amp\Ipc;

use Amp\Deferred;
use Amp\Ipc\Sync\ChannelledSocket;
use Amp\Loop;
use Amp\Promise;
use Amp\Success;
class IpcServer
{
    public const TYPE_AUTO = 0;
    public const TYPE_UNIX = 1;
    public const TYPE_FIFO = 2;
    public const TYPE_TCP = 4;
    /** @var resource|null */
    private $server;
    /** @var Deferred */
    private $acceptor;
    /** @var string|null */
    private $watcher;
    /** @var string|null */
    private $uri;
    /**
     * @param string $uri Local endpoint on which to listen for requests
     * @param self::TYPE_* $type Server type
     */
    public function __construct(string $uri = '', int $type = 0)
    {
        if (!$uri) {
            $suffix = \bin2hex(\random_bytes(10));
            $uri = \sys_get_temp_dir() . "/amp-ipc-" . $suffix . ".sock";
        }
        if (\file_exists($uri)) {
            @\unlink($uri);
        }
        $this->uri = $uri;
        $isWindows = \strncasecmp(\PHP_OS, "WIN", 3) === 0;
        if ($isWindows) {
            if ($type === self::TYPE_AUTO || $type === self::TYPE_TCP) {
                $types = [self::TYPE_TCP];
            } else {
                throw new \RuntimeException("Cannot use FIFOs and UNIX sockets on windows");
            }
        } elseif ($type === self::TYPE_AUTO) {
            $types = [];
            if (\strlen($uri) <= 104) {
                $types[] = self::TYPE_UNIX;
            }
            $types[] = self::TYPE_FIFO;
            $types[] = self::TYPE_TCP;
        } else {
            $types = [];
            if ($type & self::TYPE_UNIX && \strlen($uri) <= 104) {
                $types[] = self::TYPE_UNIX;
            }
            if ($type & self::TYPE_TCP) {
                $types[] = self::TYPE_TCP;
            }
            if ($type & self::TYPE_FIFO) {
                $types[] = self::TYPE_FIFO;
            }
        }
        $errors = [];
        foreach ($types as $type) {
            if ($type === self::TYPE_FIFO) {
                try {
                    if (!\posix_mkfifo($uri, 0777)) {
                        $errors[$type] = "could not create the FIFO socket";
                        continue;
                    }
                } catch (\Throwable $e) {
                    $errors[$type] = "could not create the FIFO socket: {$e}";
                    continue;
                }
                $error = '';
                try {
                    // Open in r+w mode to prevent blocking if there is no reader
                    $this->server = \fopen($uri, 'r+');
                } catch (\Throwable $e) {
                    $error = "{$e}";
                }
                if ($this->server) {
                    \stream_set_blocking($this->server, false);
                    break;
                }
                $errors[$type] = "could not connect to the FIFO socket: {$error}";
            } else {
                $listenUri = $type === self::TYPE_TCP ? "tcp://127.0.0.1:0" : "unix://" . $uri;
                $errno = -1;
                $errstr = 'no error';
                try {
                    $this->server = \stream_socket_server($listenUri, $errno, $errstr, \STREAM_SERVER_BIND | \STREAM_SERVER_LISTEN);
                } catch (\Throwable $e) {
                    $errstr = "exception: {$e}";
                }
                if ($this->server) {
                    if ($type === self::TYPE_TCP) {
                        try {
                            $name = \stream_socket_get_name($this->server, false);
                            $port = \Phabel\Target\Php80\Polyfill::substr($name, \strrpos($name, ":") + 1);
                            if (!\file_put_contents($this->uri, "tcp://127.0.0.1:" . $port)) {
                                $errors[$type] = 'could not create URI file';
                                $this->server = null;
                            }
                        } catch (\Throwable $e) {
                            $errors[$type] = "could not create URI file: {$e}";
                            $this->server = null;
                        }
                        if (!$this->server) {
                            continue;
                        }
                    }
                    break;
                }
                $errors[$type] = "(errno: {$errno}) {$errstr}";
            }
        }
        if (!$this->server) {
            throw new IpcServerException($errors);
        }
        $acceptor =& $this->acceptor;
        $this->watcher = Loop::onReadable($this->server, static function (string $watcher, $server) use(&$acceptor, $type) : void {
            if ($type === self::TYPE_FIFO) {
                $length = \unpack('v', \fread($server, 2))[1];
                if (!$length) {
                    return;
                    // Could not accept, wrong length read
                }
                $prefix = \fread($server, $length);
                $sockets = [$prefix . '1', $prefix . '2'];
                foreach ($sockets as $k => &$socket) {
                    if (@\filetype($socket) !== 'fifo') {
                        if ($k) {
                            \fclose($sockets[0]);
                        }
                        return;
                        // Is not a FIFO
                    }
                    // Open in either read or write mode to send a close signal when done
                    if (!($socket = \fopen($socket, $k ? 'w' : 'r'))) {
                        if ($k) {
                            \fclose($sockets[0]);
                        }
                        return;
                        // Could not open fifo
                    }
                }
                $channel = new ChannelledSocket(...$sockets);
            } else {
                // Error reporting suppressed since stream_socket_accept() emits E_WARNING on client accept failure.
                if (!($client = @\stream_socket_accept($server, 0))) {
                    // Timeout of 0 to be non-blocking.
                    return;
                    // Accepting client failed.
                }
                $channel = new ChannelledSocket($client, $client);
            }
            $deferred = $acceptor;
            $acceptor = null;
            \assert($deferred !== null);
            $deferred->resolve($channel);
            if (!$acceptor) {
                Loop::disable($watcher);
            }
        });
        Loop::disable($this->watcher);
    }
    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }
    /**
     * @return Promise<(ChannelledSocket | null)>
     *
     * @throws PendingAcceptError If another accept request is pending.
     */
    public function accept() : Promise
    {
        if ($this->acceptor) {
            throw new PendingAcceptError();
        }
        if (!$this->server) {
            return new Success();
            // Resolve with null when server is closed.
        }
        $this->acceptor = new Deferred();
        Loop::enable($this->watcher);
        return $this->acceptor->promise();
    }
    /**
     * Closes the server and stops accepting connections. Any socket clients accepted will not be closed.
     */
    public function close() : void
    {
        Loop::cancel($this->watcher);
        if ($this->acceptor) {
            $acceptor = $this->acceptor;
            $this->acceptor = null;
            $acceptor->resolve();
        }
        if ($this->server) {
            \fclose($this->server);
            $this->server = null;
        }
        if ($this->uri !== null) {
            @\unlink($this->uri);
            $this->uri = null;
        }
    }
    /**
     * @return bool
     */
    public function isClosed() : bool
    {
        return $this->server === null;
    }
    /**
     * References the accept watcher.
     *
     * @see Loop::reference()
     */
    public final function reference() : void
    {
        Loop::reference($this->watcher);
    }
    /**
     * Unreferences the accept watcher.
     *
     * @see Loop::unreference()
     */
    public final function unreference() : void
    {
        Loop::unreference($this->watcher);
    }
    /**
     * Get endpoint to which clients should connect.
     *
     * @return string
     */
    public function getUri() : string
    {
        return $this->uri;
    }
}