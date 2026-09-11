<?php

declare(strict_types=1);

namespace Boatrace\Support\Browser;

use Boatrace\Types\Contracts\Browser\BrowserObserver as BrowserObserverContract;
use Boatrace\Types\Contracts\Timing\TimingDispatcher as TimingDispatcherContract;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @author shimomo
 */
final class ObservedHttpBrowser extends HttpBrowser
{
    /**
     * @param \Boatrace\Types\Contracts\Timing\TimingDispatcher $timing
     * @param \Boatrace\Types\Contracts\Browser\BrowserObserver $observer
     * @param ?\Symfony\Contracts\HttpClient\HttpClientInterface $httpClient
     */
    public function __construct(
        private readonly TimingDispatcherContract $timing,
        private readonly BrowserObserverContract $observer,
        ?HttpClientInterface $httpClient = null,
    ) {
        parent::__construct($httpClient);
    }

    /**
     * @param \Symfony\Component\BrowserKit\Request $request
     * @return \Symfony\Component\BrowserKit\Response
     */
    #[\Override]
    protected function doRequest(object $request): Response
    {
        $response = parent::doRequest($request);

        /** @var list<string>|string|null $serverTiming */
        $serverTiming = $response->getHeader('server-timing', false);

        $this->observer->observe(
            $request->getMethod(),
            $request->getUri(),
            $response,
            $this->timing->parse($serverTiming)
        );

        return $response;
    }
}
