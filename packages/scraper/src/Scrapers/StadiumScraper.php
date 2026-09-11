<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Scrapers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Scraper\StadiumScraper as StadiumScraperContract;
use Boatrace\Types\Enums\Stadium;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class StadiumScraper implements StadiumScraperContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-string
     */
    private const string BASE_URL = 'https://www.boatrace.jp';

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     */
    public function __construct(
        private readonly BrowserDispatcherContract $browser,
        private readonly ConverterDispatcherContract $converter,
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

        $scraperFormat = '%s/owpc/pc/race/index?hd=%s';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'));
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $stadiums = $scraper
            ->filter('.table1')
            ->eq(0)
            ->filter('table tbody td.is-arrow1.is-fBold.is-fs15')
            ->each(function (Crawler $element): array {
                $stadiumName = $element->filter('a')->filter('img')->attr('alt');
                if ($stadiumName === null || $stadiumName === '') {
                    return [];
                }

                $stadiumName = str_replace('>', '', $stadiumName);
                if ($stadiumName === '') {
                    return [];
                }

                $stadiumNumber = $this->converter->toEnumOrNull(
                    fn() => Stadium::fromName($stadiumName)
                )?->value;

                if ($stadiumNumber === null) {
                    return [];
                }

                return [$stadiumNumber => $stadiumName];
            });

        $response = [];

        foreach ($stadiums as $stadium) {
            foreach ($stadium as $number => $name) {
                $response[$number] = $name;
            }
        }

        return $response;
    }
}
