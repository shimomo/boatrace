<?php

declare(strict_types=1);

namespace Boatrace\Support\Browser;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Browser\BrowserObserver as BrowserObserverContract;
use Boatrace\Types\Contracts\Timing\TimingDispatcher as TimingDispatcherContract;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use ValueError;

/**
 * @author shimomo
 */
final class BrowserDispatcher implements BrowserDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @var int
     */
    private const int ANCHOR_MAJOR = 151;

    /**
     * @var non-empty-string
     */
    private const string ANCHOR_DATE = '2026-07-28';

    /**
     * @var int
     */
    private const int RELEASE_INTERVAL_DAYS = 28;

    /**
     * @var int
     */
    private const int LEAD_MAJORS = 2;

    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private const array VERSIONED_SERVER_PARAMETERS = [
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 ' .
            '(KHTML, like Gecko) Chrome/%1$d.0.0.0 Safari/537.36',
        'HTTP_SEC_CH_UA' => '"Google Chrome";v="%1$d", "Chromium";v="%1$d", "Not)A;Brand";v="24"',
    ];

    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private const array DEFAULT_SERVER_PARAMETERS = [
        'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'HTTP_ACCEPT_LANGUAGE' => 'ja,en-US;q=0.9,en;q=0.8',
        'HTTP_CACHE_CONTROL' => 'max-age=0',
        'HTTP_CONNECTION' => 'keep-alive',
        'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
        'HTTP_SEC_CH_UA_PLATFORM' => '"Windows"',
        'HTTP_SEC_CH_UA_MOBILE' => '?0',
        'HTTP_SEC_FETCH_SITE' => 'none',
        'HTTP_SEC_FETCH_MODE' => 'navigate',
        'HTTP_SEC_FETCH_USER' => '?1',
        'HTTP_SEC_FETCH_DEST' => 'document',
        'HTTP_PRIORITY' => 'u=0, i',
    ];

    /**
     * @var array<non-empty-string, float>
     */
    private const array DEFAULT_HTTP_CLIENT_OPTIONS = [
        'timeout' => 15.0,
        'max_duration' => 30.0,
    ];

    /**
     * @param array<non-empty-string, non-empty-string> $serverParameters
     * @param ?\DateTimeInterface $now
     * @param ?\Boatrace\Types\Contracts\Timing\TimingDispatcher $timing
     * @param ?\Boatrace\Types\Contracts\Browser\BrowserObserver $observer
     * @param int $maxRetries
     * @param ?\Symfony\Contracts\HttpClient\HttpClientInterface $httpClient
     * @param array<non-empty-string, float> $httpClientOptions
     * @throws \InvalidArgumentException
     * @throws \ValueError
     */
    public function __construct(
        private readonly array $serverParameters = [],
        private readonly ?DateTimeInterface $now = null,
        private readonly ?TimingDispatcherContract $timing = null,
        private readonly ?BrowserObserverContract $observer = null,
        private readonly int $maxRetries = 3,
        private readonly ?HttpClientInterface $httpClient = null,
        private readonly array $httpClientOptions = [],
    ) {
        if ($this->observer !== null && $this->timing === null) {
            throw new InvalidArgumentException(
                'A `' . TimingDispatcherContract::class . '` is required when an observer is given.'
            );
        }

        if ($this->maxRetries < 0) {
            throw new ValueError(sprintf('$maxRetries must be 0 or greater, %d given.', $this->maxRetries));
        }
    }

    /**
     * @param array<non-empty-string, non-empty-string> $serverParameters
     * @return \Symfony\Component\BrowserKit\HttpBrowser
     */
    #[\Override]
    public function create(array $serverParameters = []): HttpBrowser
    {
        $httpClient = $this->createHttpClient();

        $httpBrowser = $this->timing !== null && $this->observer !== null
            ? new ObservedHttpBrowser($this->timing, $this->observer, $httpClient)
            : new HttpBrowser($httpClient);

        $httpBrowser->setServerParameters(array_merge(
            $this->versionedServerParameters(),
            self::DEFAULT_SERVER_PARAMETERS,
            $this->serverParameters,
            $serverParameters
        ));

        return $httpBrowser;
    }

    /**
     * @return \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    private function createHttpClient(): HttpClientInterface
    {
        $httpClient = $this->httpClient ?? HttpClient::create($this->getHttpClientOptions());

        if ($this->maxRetries === 0) {
            return $httpClient;
        }

        return new RetryableHttpClient($httpClient, new GenericRetryStrategy(), $this->maxRetries);
    }

    /**
     * @return array<non-empty-string, float>
     */
    #[\Override]
    public function getHttpClientOptions(): array
    {
        return [...self::DEFAULT_HTTP_CLIENT_OPTIONS, ...$this->httpClientOptions];
    }

    /**
     * @return array<non-empty-string, non-empty-string>
     */
    private function versionedServerParameters(): array
    {
        $chromeMajorVersion = $this->chromeMajorVersion();

        return array_map(
            /**
             * @param non-empty-string $template
             * @return non-empty-string
             */
            static fn(string $template): string => sprintf($template, $chromeMajorVersion),
            self::VERSIONED_SERVER_PARAMETERS
        );
    }

    /**
     * @return int
     */
    private function chromeMajorVersion(): int
    {
        $timeZone = new DateTimeZone('UTC');
        $anchor = new DateTimeImmutable(self::ANCHOR_DATE, $timeZone);
        $now = new DateTimeImmutable(($this->now ?? new DateTimeImmutable())->format('Y-m-d'), $timeZone);

        $elapsedDays = $now > $anchor ? (int) $anchor->diff($now)->days : 0;

        return self::ANCHOR_MAJOR
            + intdiv($elapsedDays, self::RELEASE_INTERVAL_DAYS)
            + self::LEAD_MAJORS;
    }
}
