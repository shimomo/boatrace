<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Browser;

use Boatrace\Support\Browser\ObservedHttpBrowser;
use Boatrace\Support\Timing\TimingDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author shimomo
 */
final class ObservedHttpBrowserTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Tests\Browser\RecordingBrowserObserver
     */
    protected RecordingBrowserObserver $observer;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->observer = new RecordingBrowserObserver();
    }

    /**
     * @return void
     */
    #[Test]
    public function observesEveryRequest(): void
    {
        $this->createBrowser([['cdn-cache; desc=MISS', 'edge; dur=8012', 'origin; dur=23']])
            ->request('GET', 'https://www.boatrace.jp/owpc/pc/race/index');

        $this->assertCount(1, $this->observer->calls);
        $this->assertSame('GET', $this->observer->calls[0]['method']);
        $this->assertSame(
            'https://www.boatrace.jp/owpc/pc/race/index',
            $this->observer->calls[0]['uri']
        );
        $this->assertSame(200, $this->observer->calls[0]['statusCode']);
        $this->assertSame([
            'cdn-cache' => ['dur' => null, 'desc' => 'MISS'],
            'edge' => ['dur' => 8012.0, 'desc' => null],
            'origin' => ['dur' => 23.0, 'desc' => null],
        ], $this->observer->calls[0]['timings']);
    }

    /**
     * @return void
     */
    #[Test]
    public function observesRequestsWithoutServerTiming(): void
    {
        $this->createBrowser([[]])->request('GET', 'https://race.boatcast.jp/');

        $this->assertCount(1, $this->observer->calls);
        $this->assertSame([], $this->observer->calls[0]['timings']);
    }

    /**
     * @return void
     */
    #[Test]
    public function observesEachRequestSeparately(): void
    {
        $browser = $this->createBrowser([['edge; dur=12'], ['edge; dur=8033']]);

        $browser->request('GET', 'https://www.boatrace.jp/owpc/pc/race/index');
        $browser->request('GET', 'https://www.boatrace.jp/owpc/pc/race/racelist');

        $this->assertSame(12.0, $this->observer->calls[0]['timings']['edge']['dur']);
        $this->assertSame(8033.0, $this->observer->calls[1]['timings']['edge']['dur']);
    }

    /**
     * @return void
     */
    #[Test]
    public function observesTheResponseBody(): void
    {
        $this->createBrowser([[]], "data=\t\nZZZ\t\n")
            ->request('GET', 'https://race.boatcast.jp/txt/22/bc_api_hyousu3_20260816_22_01.txt');

        $this->assertSame("data=\t\nZZZ\t\n", $this->observer->calls[0]['content']);
    }

    /**
     * @param list<list<string>> $serverTimings
     * @param string $content
     * @return \Boatrace\Support\Browser\ObservedHttpBrowser
     */
    private function createBrowser(array $serverTimings, string $content = ''): ObservedHttpBrowser
    {
        $responses = array_map(
            fn(array $serverTiming): MockResponse => new MockResponse($content, [
                'response_headers' => $serverTiming === [] ? [] : ['server-timing' => $serverTiming],
            ]),
            $serverTimings
        );

        return new ObservedHttpBrowser(
            new TimingDispatcher(),
            $this->observer,
            new MockHttpClient($responses)
        );
    }
}
