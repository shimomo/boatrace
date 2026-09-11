<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Browser;

use BadMethodCallException;
use Boatrace\Support\Browser\BrowserDispatcher;
use Boatrace\Support\Browser\ObservedHttpBrowser;
use Boatrace\Support\Timing\TimingDispatcher;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use ValueError;

/**
 * @author shimomo
 */
final class BrowserDispatcherTest extends TestCase
{
    /**
     * @var int
     */
    private const int MEASURED_TARPIT_FLOOR = 146;

    /**
     * @var non-empty-string
     */
    private const string ANCHOR_DATE = '2026-07-28';

    /**
     * @var int
     */
    private const int ANCHOR_MAJOR = 151;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Browser\BrowserDispatcher
     */
    protected BrowserDispatcher $browser;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->browser = new BrowserDispatcher();
    }

    /**
     * @return void
     */
    #[Test]
    public function createReturnsHttpBrowser(): void
    {
        $this->assertInstanceOf(HttpBrowser::class, $this->browser->create());
    }

    /**
     * @return void
     */
    #[Test]
    public function createReturnsHttpBrowserWithDefaultServerParameters(): void
    {
        $httpBrowser = $this->browser->create();

        $this->assertStringContainsString(
            'Mozilla/5.0',
            (string) $httpBrowser->getServerParameter('HTTP_USER_AGENT')
        );
        $this->assertSame('ja,en-US;q=0.9,en;q=0.8', $httpBrowser->getServerParameter('HTTP_ACCEPT_LANGUAGE'));
    }

    /**
     * @return void
     */
    #[Test]
    public function createClaimsTheAnchorVersionOnTheAnchorDate(): void
    {
        $this->assertSame(self::ANCHOR_MAJOR + 2, $this->chromeMajorVersionOn(self::ANCHOR_DATE));
    }

    /**
     * @return void
     */
    #[Test]
    public function createAdvancesOneMajorEveryFourWeeks(): void
    {
        $this->assertSame(153, $this->chromeMajorVersionOn('2026-08-24'));
        $this->assertSame(154, $this->chromeMajorVersionOn('2026-08-25'));
        $this->assertSame(166, $this->chromeMajorVersionOn('2027-08-07'));
    }

    /**
     * @return void
     */
    #[Test]
    public function createNeverGoesBackwardsBeforeTheAnchor(): void
    {
        $this->assertSame(self::ANCHOR_MAJOR + 2, $this->chromeMajorVersionOn('2020-01-01'));
    }

    /**
     * @return void
     */
    #[Test]
    public function createStaysAboveTheMeasuredTarpitFloorForTheNextDecade(): void
    {
        $anchor = new DateTimeImmutable(self::ANCHOR_DATE);

        for ($days = 0; $days <= 3650; $days += 7) {
            $date = $anchor->modify('+' . $days . ' days')->format('Y-m-d');

            $this->assertGreaterThanOrEqual(
                self::MEASURED_TARPIT_FLOOR,
                $this->chromeMajorVersionOn($date),
                $date
            );
        }
    }

    /**
     * @return void
     */
    #[Test]
    public function createKeepsTheUserAgentAndClientHintOnTheSameVersion(): void
    {
        $httpBrowser = $this->browser->create();
        $chromeMajorVersion = $this->chromeMajorVersionOn('now');

        $this->assertStringContainsString(
            'Chrome/' . $chromeMajorVersion . '.0.0.0',
            (string) $httpBrowser->getServerParameter('HTTP_USER_AGENT')
        );
        $this->assertStringContainsString(
            '"Google Chrome";v="' . $chromeMajorVersion . '"',
            (string) $httpBrowser->getServerParameter('HTTP_SEC_CH_UA')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function createSendsTheHeadersThatKeepTheRequestOutOfTheTarpit(): void
    {
        $httpBrowser = $this->browser->create();

        foreach ([
            'HTTP_SEC_CH_UA',
            'HTTP_SEC_CH_UA_PLATFORM',
            'HTTP_SEC_CH_UA_MOBILE',
            'HTTP_SEC_FETCH_SITE',
            'HTTP_SEC_FETCH_MODE',
            'HTTP_SEC_FETCH_USER',
            'HTTP_SEC_FETCH_DEST',
        ] as $header) {
            $this->assertNotSame('', (string) $httpBrowser->getServerParameter($header), $header);
        }
    }

    /**
     * @return void
     */
    #[Test]
    public function createOverridesServerParameters(): void
    {
        $httpBrowser = $this->browser->create(['HTTP_USER_AGENT' => 'Boatrace/1.0']);

        $this->assertSame('Boatrace/1.0', $httpBrowser->getServerParameter('HTTP_USER_AGENT'));
    }

    /**
     * @return void
     */
    #[Test]
    public function createOverridesServerParametersGivenToConstructor(): void
    {
        $browser = new BrowserDispatcher(['HTTP_ACCEPT_LANGUAGE' => 'en-US']);

        $this->assertSame('en-US', $browser->create()->getServerParameter('HTTP_ACCEPT_LANGUAGE'));
    }

    /**
     * @return void
     */
    #[Test]
    public function createReturnsPlainHttpBrowserWithoutObserver(): void
    {
        $this->assertNotInstanceOf(ObservedHttpBrowser::class, $this->browser->create());
    }

    /**
     * @return void
     */
    #[Test]
    public function createReturnsObservedHttpBrowserWithObserver(): void
    {
        $browser = new BrowserDispatcher([], null, new TimingDispatcher(), new RecordingBrowserObserver());

        $this->assertInstanceOf(ObservedHttpBrowser::class, $browser->create());
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsInvalidArgumentExceptionWhenObserverIsGivenWithoutTiming(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs(
            'A `Boatrace\Types\Contracts\Timing\TimingDispatcher` is required when an observer is given.'
        );

        new BrowserDispatcher([], null, null, new RecordingBrowserObserver());
    }

    /**
     * @return void
     */
    #[Test]
    public function createRetriesTransportFailures(): void
    {
        $httpClient = new MockHttpClient([
            $this->connectionResetResponse(),
            $this->okResponse(),
        ]);

        $browser = new BrowserDispatcher([], null, null, null, 3, $httpClient);
        $browser->create()->request('GET', 'https://www.boatrace.jp/');

        $this->assertSame(2, $httpClient->getRequestsCount());
    }

    /**
     * @return void
     */
    #[Test]
    public function boundsEveryAttemptByDefault(): void
    {
        $options = (new BrowserDispatcher())->getHttpClientOptions();

        $this->assertGreaterThan(0.0, $options['timeout']);
        $this->assertGreaterThan(0.0, $options['max_duration']);
    }

    /**
     * @return void
     */
    #[Test]
    public function letsTheCallerTightenTheBounds(): void
    {
        $browser = new BrowserDispatcher([], null, null, null, 3, null, ['max_duration' => 5.0]);

        $options = $browser->getHttpClientOptions();

        $this->assertSame(5.0, $options['max_duration']);
        $this->assertGreaterThan(0.0, $options['timeout'], '触っていない側は既定のまま残る');
    }

    /**
     * @return void
     */
    #[Test]
    public function createGivesUpAfterMaxRetries(): void
    {
        $httpClient = new MockHttpClient([
            $this->connectionResetResponse(),
            $this->connectionResetResponse(),
            $this->connectionResetResponse(),
            $this->connectionResetResponse(),
        ]);

        $browser = new BrowserDispatcher([], null, null, null, 3, $httpClient);

        try {
            $browser->create()->request('GET', 'https://www.boatrace.jp/');
            $this->fail('Expected a ' . TransportException::class . '.');
        } catch (TransportException) {
            $this->assertSame(4, $httpClient->getRequestsCount());
        }
    }

    /**
     * @return void
     */
    #[Test]
    public function createBacksOffBetweenRetries(): void
    {
        $delays = [];
        $httpClient = new MockHttpClient([
            $this->connectionResetResponse($delays),
            $this->connectionResetResponse($delays),
            $this->okResponse($delays),
        ]);

        $browser = new BrowserDispatcher([], null, null, null, 3, $httpClient);
        $browser->create()->request('GET', 'https://www.boatrace.jp/');

        $this->assertCount(2, $delays);
        $this->assertGreaterThan(0.0, $delays[0]);
        $this->assertGreaterThan($delays[0], $delays[1]);
    }

    /**
     * @return void
     */
    #[Test]
    public function createDoesNotRetryWhenMaxRetriesIsZero(): void
    {
        $httpClient = new MockHttpClient([
            $this->connectionResetResponse(),
            $this->okResponse(),
        ]);

        $browser = new BrowserDispatcher([], null, null, null, 0, $httpClient);

        try {
            $browser->create()->request('GET', 'https://www.boatrace.jp/');
            $this->fail('Expected a ' . TransportException::class . '.');
        } catch (TransportException) {
            $this->assertSame(1, $httpClient->getRequestsCount());
        }
    }

    /**
     * @return void
     */
    #[Test]
    public function createRetriesTransportFailuresWithObserver(): void
    {
        $httpClient = new MockHttpClient([
            $this->connectionResetResponse(),
            $this->okResponse(),
        ]);

        $browser = new BrowserDispatcher(
            [],
            null,
            new TimingDispatcher(),
            new RecordingBrowserObserver(),
            3,
            $httpClient
        );
        $browser->create()->request('GET', 'https://www.boatrace.jp/');

        $this->assertSame(2, $httpClient->getRequestsCount());
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsValueErrorWhenMaxRetriesIsNegative(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$maxRetries must be 0 or greater, -1 given.');

        new BrowserDispatcher([], null, null, null, -1);
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Browser\BrowserDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->browser->ghost();
    }

    /**
     * @param list<float> $delays
     * @return \Symfony\Component\HttpClient\Response\MockResponse
     */
    private function connectionResetResponse(array &$delays = []): MockResponse
    {
        return $this->mockResponse('', ['error' => 'Recv failure: Connection reset by peer'], $delays);
    }

    /**
     * @param list<float> $delays
     * @return \Symfony\Component\HttpClient\Response\MockResponse
     */
    private function okResponse(array &$delays = []): MockResponse
    {
        return $this->mockResponse('<html lang="ja"></html>', [], $delays);
    }

    /**
     * @param string $body
     * @param array<non-empty-string, mixed> $info
     * @param list<float> $delays
     * @return \Symfony\Component\HttpClient\Response\MockResponse
     */
    private function mockResponse(string $body, array $info, array &$delays = []): MockResponse
    {
        return new MockResponse($body, $info + [
            'pause_handler' => static function (float $duration) use (&$delays): void {
                $delays[] = $duration;
            },
        ]);
    }

    /**
     * @param non-empty-string $date
     * @return int
     */
    private function chromeMajorVersionOn(string $date): int
    {
        $userAgent = (string) (new BrowserDispatcher([], new DateTimeImmutable($date)))
            ->create()
            ->getServerParameter('HTTP_USER_AGENT');

        $this->assertSame(1, preg_match('/Chrome\/(\d+)\./', $userAgent, $matches), $userAgent);

        return (int) $matches[1];
    }
}
