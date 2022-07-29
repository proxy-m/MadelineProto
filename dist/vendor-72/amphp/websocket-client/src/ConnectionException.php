<?php

namespace Amp\Websocket\Client;

use Amp\Http\Client\HttpException;
use Amp\Http\Client\Response;
final class ConnectionException extends HttpException
{
    /** @var Response */
    private $response;
    /**
     *
     * @param string $message
     * @param Response $response
     * @param (\Throwable | null) $previous
     */
    public function __construct($message, $response, $previous = NULL)
    {
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #1 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!$response instanceof Response) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($response) must be of type Response, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($response) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!($previous instanceof \Throwable || \is_null($previous))) {
            throw new \TypeError(__METHOD__ . '(): Argument #3 ($previous) must be of type ?Throwable, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($previous) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct($message, 0, $previous);
        $this->response = $response;
    }
    /**
     *
     */
    public function getResponse() : Response
    {
        return $this->response;
    }
}