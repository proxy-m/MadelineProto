<?php

namespace Amp\Http\Client;

final class InvalidRequestException extends HttpException
{
    /** @var Request */
    private $request;
    /**
     *
     * @param Request $request
     * @param string $message
     * @param int $code
     */
    public function __construct($request, $message, $code = 0, \Throwable $previous = NULL)
    {
        if (!$request instanceof Request) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($request) must be of type Request, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($request) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        if (!\is_string($message)) {
            if (!(\is_string($message) || \is_object($message) && \method_exists($message, '__toString') || (\is_bool($message) || \is_numeric($message)))) {
                throw new \TypeError(__METHOD__ . '(): Argument #2 ($message) must be of type string, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($message) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $message = (string) $message;
            }
        }
        if (!\is_int($code)) {
            if (!(\is_bool($code) || \is_numeric($code))) {
                throw new \TypeError(__METHOD__ . '(): Argument #3 ($code) must be of type int, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($code) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
            } else {
                $code = (int) $code;
            }
        }
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }
    /**
     *
     */
    public function getRequest() : Request
    {
        return $this->request;
    }
}