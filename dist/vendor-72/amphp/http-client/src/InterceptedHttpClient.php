<?php

namespace Amp\Http\Client;

use Amp\CancellationToken;
use Amp\Http\Client\Internal\ForbidCloning;
use Amp\Http\Client\Internal\ForbidSerialization;
use Amp\Promise;
use function Amp\call;
final class InterceptedHttpClient implements DelegateHttpClient
{
    use ForbidCloning;
    use ForbidSerialization;
    /** @var DelegateHttpClient */
    private $httpClient;
    /** @var ApplicationInterceptor */
    private $interceptor;
    /**
     *
     */
    public function __construct(DelegateHttpClient $httpClient, ApplicationInterceptor $interceptor)
    {
        $this->httpClient = $httpClient;
        $this->interceptor = $interceptor;
    }
    /**
     *
     * @param CancellationToken $cancellation
     */
    public function request(Request $request, $cancellation) : Promise
    {
        if (!$cancellation instanceof CancellationToken) {
            throw new \TypeError(__METHOD__ . '(): Argument #2 ($cancellation) must be of type CancellationToken, ' . \Phabel\Plugin\TypeHintReplacer::getDebugType($cancellation) . ' given, called in ' . \Phabel\Plugin\TypeHintReplacer::trace());
        }
        return call(function () use($request, $cancellation) {
            foreach ($request->getEventListeners() as $eventListener) {
                (yield $eventListener->startRequest($request));
            }
            return $this->interceptor->request($request, $cancellation, $this->httpClient);
        });
    }
}