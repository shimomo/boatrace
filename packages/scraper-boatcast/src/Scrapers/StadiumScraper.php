<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Scrapers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\BoatcastScraper\StadiumScraper as StadiumScraperContract;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Json\JsonDispatcher as JsonDispatcherContract;
use Boatrace\Types\Enums\Stadium;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class StadiumScraper implements StadiumScraperContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-string
     */
    private const string BASE_URL = 'https://race.boatcast.jp';

    /**
     * @var int
     */
    private const int SUCCESS_CODE = 0;

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     * @param \Boatrace\Types\Contracts\Json\JsonDispatcher $json
     */
    public function __construct(
        private readonly BrowserDispatcherContract $browser,
        private readonly ConverterDispatcherContract $converter,
        private readonly JsonDispatcherContract $json,
    ) {
        //
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<int<1, 24>, non-empty-string>
     */
    #[\Override]
    public function scrape(
        DateTimeInterface|string $date,
        ?HttpBrowser $httpBrowser = null,
    ): array {
        $date = Carbon::parse($date);

        $scraperFormat = '%s/api_txt/getHoldingList2_%s.json';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'));

        $response = [];

        foreach ($this->request($scraperUrl, $httpBrowser) as $holding) {
            if (!is_array($holding)) {
                continue;
            }

            $stadiumNumber = $holding['RaceStudiumNo'] ?? null;

            if (!is_string($stadiumNumber) && !is_int($stadiumNumber)) {
                continue;
            }

            $stadium = Stadium::tryFrom($this->converter->toIntStrict($stadiumNumber));

            if ($stadium === null) {
                continue;
            }

            $response[$stadium->value] = $stadium->name();
        }

        ksort($response, SORT_NUMERIC);

        return $response;
    }

    /**
     * @param string $url
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return list<mixed>
     */
    private function request(string $url, ?HttpBrowser $httpBrowser): array
    {
        $httpBrowser ??= $this->browser->create();
        $httpBrowser->request('GET', $url);

        $httpResponse = $httpBrowser->getInternalResponse();

        if ($httpResponse->getStatusCode() !== 200) {
            return [];
        }

        $decoded = $this->json->decode($httpResponse->getContent());

        if (($decoded['res_cd'] ?? null) !== self::SUCCESS_CODE) {
            return [];
        }

        /** @var mixed $holdings */
        $holdings = $decoded['return_info'] ?? null;

        return is_array($holdings) ? array_values($holdings) : [];
    }
}
