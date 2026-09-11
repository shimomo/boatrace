<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Scrapers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use Boatrace\Types\Contracts\Tsv\TsvDispatcher as TsvDispatcherContract;
use Boatrace\Types\Enums\Absence;
use Carbon\CarbonImmutable as Carbon;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Response;
use Throwable;

/**
 * @author shimomo
 */
abstract class BoatcastFileScraper
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-string
     */
    protected const string BASE_URL = 'https://race.boatcast.jp';

    /**
     * @var non-empty-string
     */
    protected const string SENTINEL = 'data=';

    /**
     * @var non-empty-string
     */
    protected const string HTTP_DATE_FORMAT = 'D, d M Y H:i:s \\G\\M\\T';

    /**
     * @var array{etag: null, last_modified: null}
     */
    protected const array NO_METADATA = ['etag' => null, 'last_modified' => null];

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     * @param \Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher $throttler
     * @param \Boatrace\Types\Contracts\Tsv\TsvDispatcher $tsv
     */
    public function __construct(
        protected readonly BrowserDispatcherContract $browser,
        protected readonly ConverterDispatcherContract $converter,
        protected readonly ThrottlerDispatcherContract $throttler,
        protected readonly TsvDispatcherContract $tsv,
    ) {
        //
    }

    /**
     * @return non-empty-string
     */
    abstract protected function fileName(): string;

    /**
     * @return non-empty-string
     */
    abstract protected function onSalePrefix(): string;

    /**
     * @param \Carbon\CarbonImmutable $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param int<1, 3> $type
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @param bool $onSaleOnly
     * @return array{list<list<string>>, array{is_fixed: bool, etag: ?string, last_modified: ?string, reason: ?int}}
     */
    protected function request(
        Carbon $date,
        int $stadiumNumber,
        int $raceNumber,
        int $type,
        ?HttpBrowser $httpBrowser,
        bool $onSaleOnly
    ): array {
        $scraperFormat = '%s/txt/%02d/bc_%s_%s%d_%s_%02d_%02d.txt';

        $arguments = [
            static::BASE_URL,
            $stadiumNumber,
            'kakutei',
            $this->fileName(),
            $type,
            $date->format('Ymd'),
            $stadiumNumber,
            $raceNumber,
        ];

        if (!$onSaleOnly) {
            [$rows, $metadata] = $this->fetch(sprintf($scraperFormat, ...$arguments), $httpBrowser);

            if ($rows !== []) {
                return [$rows, [
                    'is_fixed' => true,
                    'etag' => $metadata['etag'],
                    'last_modified' => $metadata['last_modified'],
                    'reason' => $metadata['reason'],
                ]];
            }
        }

        $arguments[2] = $this->onSalePrefix();

        [$rows, $metadata] = $this->fetch(sprintf($scraperFormat, ...$arguments), $httpBrowser);

        return [$rows, [
            'is_fixed' => false,
            'etag' => $metadata['etag'],
            'last_modified' => $metadata['last_modified'],
            'reason' => $metadata['reason'],
        ]];
    }

    /**
     * @param string $url
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array{list<list<string>>, array{etag: ?string, last_modified: ?string, reason: ?int}}
     */
    protected function fetch(string $url, ?HttpBrowser $httpBrowser): array
    {
        $httpBrowser ??= $this->browser->create();
        $httpBrowser->request('GET', $url);

        $httpResponse = $httpBrowser->getInternalResponse();

        if ($httpResponse->getStatusCode() !== 200) {
            return [[], [...static::NO_METADATA, 'reason' => Absence::未公開->value]];
        }

        $rows = $this->tsv->parse($httpResponse->getContent());

        if (($rows[0][0] ?? null) !== static::SENTINEL) {
            return [[], [...static::NO_METADATA, 'reason' => Absence::想定外->value]];
        }

        if (($rows[1][0] ?? null) !== Absence::ON_SALE_STATUS) {
            return [[], [...static::NO_METADATA, 'reason' => Absence::fromStatus($rows[1][0] ?? null)?->value]];
        }

        return [$rows, $this->metadata($httpResponse)];
    }

    /**
     * @param \Symfony\Component\BrowserKit\Response $httpResponse
     * @return array{etag: ?string, last_modified: ?string, reason: null}
     */
    protected function metadata(Response $httpResponse): array
    {
        $etag = $httpResponse->getHeader('etag');
        $lastModified = $httpResponse->getHeader('last-modified');

        return [
            'etag' => is_string($etag) && $etag !== '' ? $etag : null,
            'last_modified' => is_string($lastModified) && $lastModified !== ''
                ? $this->parseLastModified($lastModified)
                : null,
            'reason' => null,
        ];
    }

    /**
     * @param non-empty-string $value
     * @return ?string
     */
    protected function parseLastModified(string $value): ?string
    {
        try {
            $parsed = Carbon::createFromFormat(static::HTTP_DATE_FORMAT, $value, 'GMT');
        } catch (Throwable) {
            return null;
        }

        return $parsed?->toIso8601String();
    }
}
