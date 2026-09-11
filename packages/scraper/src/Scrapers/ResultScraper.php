<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Scrapers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Filter\FilterDispatcher as FilterDispatcherContract;
use Boatrace\Types\Contracts\Parser\RacerParser as RacerParserContract;
use Boatrace\Types\Contracts\Parser\ResultParser as ResultParserContract;
use Boatrace\Types\Contracts\Scraper\ResultScraper as ResultScraperContract;
use Boatrace\Types\Enums\Place;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class ResultScraper implements ResultScraperContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-string
     */
    private const string WIND_DIRECTION_PATTERN = '/is-wind(\d+)/u';

    /**
     * @var non-empty-string
     */
    private const string BASE_URL = 'https://www.boatrace.jp';

    /**
     * @var non-empty-string
     */
    private const string BASE_XPATH = 'descendant-or-self::body/main/div/div/div';

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array RACER_KEYS = [
        'entry_number',
        'course_number',
        'start_timing_source',
        'start_timing',
        'place_number_source',
        'place_number',
        'number_source',
        'number',
        'name',
    ];

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     * @param \Boatrace\Types\Contracts\Filter\FilterDispatcher $filter
     * @param \Boatrace\Types\Contracts\Parser\RacerParser $racerParser
     * @param \Boatrace\Types\Contracts\Parser\ResultParser $resultParser
     */
    public function __construct(
        private readonly BrowserDispatcherContract $browser,
        private readonly ConverterDispatcherContract $converter,
        private readonly FilterDispatcherContract $filter,
        private readonly RacerParserContract $racerParser,
        private readonly ResultParserContract $resultParser,
    ) {
        //
    }

    /**
     * @param \DateTimeInterface|non-empty-string $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrape(
        DateTimeInterface|string $date,
        int $stadiumNumber,
        int $raceNumber,
        ?HttpBrowser $httpBrowser = null,
    ): array {
        $date = Carbon::parse($date);

        $scraperFormat = '%s/owpc/pc/race/raceresult?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, self::BASE_XPATH);

        $baseLevel = 0;
        if ($this->filter->byXPath($scraper, $levelXPath) !== null) {
            $baseLevel = 1;
        }

        $windSpeedFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[3]/div/span[2]';
        $windSpeedXPath = sprintf($windSpeedFormat, self::BASE_XPATH, $baseLevel + 6);
        $windSpeedSource = $this->filter->byXPath($scraper, $windSpeedXPath);
        $windSpeed = $this->resultParser->parseWindSpeed($windSpeedSource);

        $windDirectionFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[4]/p';
        $windDirectionXPath = sprintf($windDirectionFormat, self::BASE_XPATH, $baseLevel + 6);
        $windDirectionSource = $this->filter->byXPathAsPattern($scraper, $windDirectionXPath, 'class', self::WIND_DIRECTION_PATTERN);
        $windDirection = $this->resultParser->parseWindDirection($windDirectionSource);

        $waveHeightFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[6]/div/span[2]';
        $waveHeightXPath = sprintf($waveHeightFormat, self::BASE_XPATH, $baseLevel + 6);
        $waveHeightSource = $this->filter->byXPath($scraper, $waveHeightXPath);
        $waveHeight = $this->resultParser->parseWaveHeight($waveHeightSource);

        $weatherFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[2]/div/span';
        $weatherXPath = sprintf($weatherFormat, self::BASE_XPATH, $baseLevel + 6);
        $weatherSource = $this->filter->byXPath($scraper, $weatherXPath);
        $weather = $this->resultParser->parseWeather($weatherSource);

        $airTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[1]/div/span[2]';
        $airTemperatureXPath = sprintf($airTemperatureFormat, self::BASE_XPATH, $baseLevel + 6);
        $airTemperatureSource = $this->filter->byXPath($scraper, $airTemperatureXPath);
        $airTemperature = $this->resultParser->parseAirTemperature($airTemperatureSource);

        $waterTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[5]/div/span[2]';
        $waterTemperatureXPath = sprintf($waterTemperatureFormat, self::BASE_XPATH, $baseLevel + 6);
        $waterTemperatureSource = $this->filter->byXPath($scraper, $waterTemperatureXPath);
        $waterTemperature = $this->resultParser->parseWaterTemperature($waterTemperatureSource);

        $techniqueFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[2]/div[2]/table/tbody/tr/td';
        $techniqueXPath = sprintf($techniqueFormat, self::BASE_XPATH, $baseLevel + 6);
        $techniqueSource = $this->filter->byXPath($scraper, $techniqueXPath);
        $technique = $this->resultParser->parseTechnique($techniqueSource);

        $remarksFormat = '%s/div[2]/div[%d]/div[2]/div[2]/table/tbody/tr/td';
        $remarksXPath = sprintf($remarksFormat, self::BASE_XPATH, $baseLevel + 6);
        $remarksSource = $this->filter->byXPath($scraper, $remarksXPath);
        $remarks = $this->resultParser->parseRemarks($remarksSource);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        $response += $windSpeed;
        $response += $windDirection;
        $response += $waveHeight;
        $response += $weather;
        $response += $airTemperature;
        $response += $waterTemperature;
        $response += $technique;
        $response += $remarks;

        $response += $this->scrapeRacers($scraper, $baseLevel);
        $response += $this->scrapePayouts($scraper, $baseLevel);
        $response += $this->scrapeRefunds($scraper, $baseLevel);

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @return array{
     *     racers: array<int, array<non-empty-string, mixed>>,
     * }
     */
    private function scrapeRacers(Crawler $scraper, int $baseLevel): array
    {
        $racers = $this->hasResultTable($scraper, $baseLevel)
            ? $this->scrapeResultTable($scraper, $baseLevel)
            : [];

        $template = array_fill_keys(self::RACER_KEYS, null);

        $response = ['racers' => []];

        foreach (range(1, 6) as $entryNumberKey) {
            $response['racers'][$entryNumberKey] = array_replace($template, [
                'entry_number' => $entryNumberKey,
            ], $racers[$entryNumberKey] ?? []);
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @return bool
     */
    private function hasResultTable(Crawler $scraper, int $baseLevel): bool
    {
        $placeFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr/td[1]';
        $placeXPath = sprintf($placeFormat, self::BASE_XPATH, $baseLevel + 5);
        $placeSource = $this->filter->byXPath($scraper, $placeXPath);

        if ($placeSource === null) {
            return false;
        }

        $shortNames = array_map(fn(Place $case): string => $case->shortName(), Place::cases());

        return in_array($placeSource, $shortNames, true);
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @return array<int, array<non-empty-string, mixed>>
     */
    private function scrapeResultTable(Crawler $scraper, int $baseLevel): array
    {
        $response = [];

        foreach (range(1, 6) as $index) {
            $entryNumberFormat = '%s/div[2]/div[%d]/div[2]/div/table/tbody/tr[%s]/td/div/span[1]';
            $entryNumberXPath = sprintf($entryNumberFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $entryNumberSource = $this->filter->byXPath($scraper, $entryNumberXPath);
            $entryNumber = $this->racerParser->parseEntryNumber($entryNumberSource);

            $course = ['course_number' => $index];

            $startTimingFormat = '%s/div[2]/div[%d]/div[2]/div/table/tbody/tr[%s]/td/div/span[3]/span';
            $startTimingXPath = sprintf($startTimingFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $startTimingSource = $this->filter->byXPath($scraper, $startTimingXPath);
            $startTiming = $this->resultParser->parseStartTiming($startTimingSource);

            if (!isset($entryNumber['entry_number'])) {
                $entryNumber['entry_number'] = $index;
                $course['course_number'] = null;
            }

            /** @var mixed $entryNumberKey */
            $entryNumberKey = $entryNumber['entry_number'];

            if (!is_int($entryNumberKey) || !in_array($entryNumberKey, range(1, 6), true)) {
                continue;
            }

            $response[$entryNumberKey] ??= [];
            $response[$entryNumberKey] += $entryNumber;
            $response[$entryNumberKey] += $course;
            $response[$entryNumberKey] += $startTiming;
        }

        foreach (range(1, 6) as $index) {
            $placeFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[1]';
            $placeXPath = sprintf($placeFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $placeSource = $this->filter->byXPath($scraper, $placeXPath);
            $place = $this->resultParser->parsePlace($placeSource);

            $entryNumberFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[2]';
            $entryNumberXPath = sprintf($entryNumberFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $entryNumberSource = $this->filter->byXPath($scraper, $entryNumberXPath);
            $entryNumber = $this->racerParser->parseEntryNumber($entryNumberSource);

            $numberFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[3]/span[1]';
            $numberXPath = sprintf($numberFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $numberSource = $this->filter->byXPath($scraper, $numberXPath);
            $number = $this->racerParser->parseNumber($numberSource);

            $nameFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[3]/span[2]';
            $nameXPath = sprintf($nameFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $nameSource = $this->filter->byXPath($scraper, $nameXPath);
            $name = $this->racerParser->parseName($nameSource);

            if (!isset($entryNumber['entry_number'])) {
                $entryNumber['entry_number'] = $index;
            }

            /** @var mixed $entryNumberKey */
            $entryNumberKey = $entryNumber['entry_number'];

            if (!is_int($entryNumberKey) || !in_array($entryNumberKey, range(1, 6), true)) {
                continue;
            }

            $response[$entryNumberKey] ??= [];
            $response[$entryNumberKey] += $entryNumber;
            $response[$entryNumberKey] += $place;
            $response[$entryNumberKey] += $number;
            $response[$entryNumberKey] += $name;
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array{
     *     payouts?: array{
     *         trifecta?: list<array{combination: ?string, amount: ?non-negative-int, label: ?string}>,
     *         trio?: list<array{combination: ?string, amount: ?non-negative-int, label: ?string}>,
     *         exacta?: list<array{combination: ?string, amount: ?non-negative-int, label: ?string}>,
     *         quinella?: list<array{combination: ?string, amount: ?non-negative-int, label: ?string}>,
     *         quinella_place?: list<array{combination: ?string, amount: ?non-negative-int, label: ?string}>,
     *         win?: list<array{combination: ?string, amount: ?non-negative-int, label: ?string}>,
     *         place?: list<array{combination: ?string, amount: ?non-negative-int, label: ?string}>,
     *     }
     * }
     */
    private function scrapePayouts(Crawler $scraper, int $baseLevel): array
    {
        $response = [];

        $combinations = $this->scrapeAllCombinations($scraper, $baseLevel);
        $amounts = $this->scrapeAllAmounts($scraper, $baseLevel);

        foreach ($combinations as $name => $values) {
            foreach ($values as $index => $value) {
                if (!isset($response['payouts'][$name])) {
                    $response['payouts'][$name] = [];
                }

                if ($value['combination'] === null && $value['label'] === null) {
                    continue;
                }

                $response['payouts'][$name][] = [
                    'combination' => $value['combination'],
                    'amount' => $amounts[$name][$index] ?? null,
                    'label' => $value['label'],
                ];
            }
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @return array{
     *     trifecta: list<array{combination: ?string, label: ?string}>,
     *     trio: list<array{combination: ?string, label: ?string}>,
     *     exacta: list<array{combination: ?string, label: ?string}>,
     *     quinella: list<array{combination: ?string, label: ?string}>,
     *     quinella_place: list<array{combination: ?string, label: ?string}>,
     *     win: list<array{combination: ?string, label: ?string}>,
     *     place: list<array{combination: ?string, label: ?string}>,
     * }
     */
    private function scrapeAllCombinations(Crawler $scraper, int $baseLevel): array
    {
        return [
            'trifecta' => $this->scrapeCombinations($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[1]/td[2]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[2]/td[1]',
            ], range(1, 5)),
            'trio' => $this->scrapeCombinations($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[1]/td[2]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[2]/td[1]',
            ], range(1, 5)),
            'exacta' => $this->scrapeCombinations($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[1]/td[2]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[2]/td[1]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[3]/td[1]',
            ], range(1, 3)),
            'quinella' => $this->scrapeCombinations($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[1]/td[2]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[2]/td[1]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[3]/td[1]',
            ], range(1, 3)),
            'quinella_place' => $this->scrapeCombinations($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[1]/td[2]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[2]/td[1]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[3]/td[1]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[4]/td[1]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[5]/td[1]',
            ], range(1, 3)),
            'win' => $this->scrapeCombinations($scraper, $baseLevel, [
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[1]/td[2]',
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[2]/td[1]',
            ], range(1, 1)),
            'place' => $this->scrapeCombinations($scraper, $baseLevel, [
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[1]/td[2]',
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[2]/td[1]',
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[3]/td[1]',
            ], range(1, 1)),
        ];
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @param list<non-empty-string> $templates
     * @param list<int> $indexes
     * @return list<array{combination: ?string, label: ?string}>
     */
    private function scrapeCombinations(
        Crawler $scraper,
        int $baseLevel,
        array $templates,
        array $indexes
    ): array {
        $response = [];

        foreach ($templates as $template) {
            $cellXPath = sprintf($template, self::BASE_XPATH, $baseLevel + 6);

            $values = [];

            foreach ($indexes as $index) {
                $values[] = $this->filter->byXPath($scraper, sprintf('%s/div/div/span[%d]', $cellXPath, $index));
            }

            $combination = implode($values);

            if ($combination !== '') {
                $response[] = ['combination' => $combination, 'label' => null];

                continue;
            }

            $label = $this->filter->byXPath($scraper, $cellXPath);

            $response[] = [
                'combination' => null,
                'label' => $label !== null && $label !== '' ? $label : null,
            ];
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @return array{
     *     trifecta: list<?non-negative-int>,
     *     trio: list<?non-negative-int>,
     *     exacta: list<?non-negative-int>,
     *     quinella: list<?non-negative-int>,
     *     quinella_place: list<?non-negative-int>,
     *     win: list<?non-negative-int>,
     *     place: list<?non-negative-int>,
     * }
     */
    private function scrapeAllAmounts(Crawler $scraper, int $baseLevel): array
    {
        return [
            'trifecta' => $this->scrapeAmounts($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[2]/td[2]/span',
            ]),
            'trio' => $this->scrapeAmounts($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[2]/td[2]/span',
            ]),
            'exacta' => $this->scrapeAmounts($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[3]/td[2]/span',
            ]),
            'quinella' => $this->scrapeAmounts($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[3]/td[2]/span',
            ]),
            'quinella_place' => $this->scrapeAmounts($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[3]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[4]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[5]/td[2]/span',
            ]),
            'win' => $this->scrapeAmounts($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[2]/td[2]/span',
            ]),
            'place' => $this->scrapeAmounts($scraper, $baseLevel, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[3]/td[2]/span',
            ]),
        ];
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @param list<non-empty-string> $templates
     * @return list<?non-negative-int>
     */
    private function scrapeAmounts(Crawler $scraper, int $baseLevel, array $templates): array
    {
        return array_map(function (string $template) use ($scraper, $baseLevel) {
            $value = $this->filter->byXPath($scraper, sprintf($template, self::BASE_XPATH, $baseLevel + 6));

            if ($value === null) {
                return null;
            }

            $value = str_replace(',', '', str_replace('¥', '', $value));

            if (!is_numeric($value)) {
                return null;
            }

            $value = $this->converter->toIntStrict($value);

            return $value >= 0 ? $value : null;
        }, $templates);
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $baseLevel
     * @return array{
     *     refunds: list<int>
     * }
     */
    private function scrapeRefunds(Crawler $scraper, int $baseLevel): array
    {
        $response = ['refunds' => []];

        foreach (range(1, 2) as $row) {
            foreach (range(1, 3) as $column) {
                $format = '%s/div[2]/div[%d]/div[2]/div[1]/div[2]/div[1]/table/tbody/tr/td/div/div[%d]/span[%d]';
                $xpath = sprintf($format, self::BASE_XPATH, $baseLevel + 6, $row, $column);
                $source = $this->filter->byXPath($scraper, $xpath);

                $entryNumber = $this->converter->toInt($source ?? '');

                if (!in_array($entryNumber, range(1, 6), true)) {
                    continue;
                }

                $response['refunds'][] = $entryNumber;
            }
        }

        return $response;
    }
}
