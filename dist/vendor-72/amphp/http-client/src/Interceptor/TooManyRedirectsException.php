<?php

namespace Amp\Http\Client\Interceptor;

use Amp\Http\Client\HttpException;
use Amp\Http\Client\Response;
class TooManyRedirectsException extends HttpException
{
    /** @var Response */
    private $response;
    /**
     *
     * @param Response $response
     */
    public function __construct($response)
    {
        if (!$response instanceof Response) {
            throw new \TypeError(__METHOD__ . '(): Argument #1 ($response) must be of type Response, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($response) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        parent::__construct("There were too many redirects");
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