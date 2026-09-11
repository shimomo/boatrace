<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Scrapers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Filter\FilterDispatcher as FilterDispatcherContract;
use Boatrace\Types\Contracts\Parser\PreviewParser as PreviewParserContract;
use Boatrace\Types\Contracts\Parser\RacerParser as RacerParserContract;
use Boatrace\Types\Contracts\Scraper\PreviewScraper as PreviewScraperContract;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class PreviewScraper implements PreviewScraperContract
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
        'weight_source',
        'weight',
        'weight_adjustment_source',
        'weight_adjustment',
        'exhibition_time_source',
        'exhibition_time',
        'tilt_adjustment_source',
        'tilt_adjustment',
        'propeller',
        'parts',
    ];

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     * @param \Boatrace\Types\Contracts\Filter\FilterDispatcher $filter
     * @param \Boatrace\Types\Contracts\Parser\RacerParser $racerParser
     * @param \Boatrace\Types\Contracts\Parser\PreviewParser $previewParser
     */
    public function __construct(
        private readonly BrowserDispatcherContract $browser,
        private readonly FilterDispatcherContract $filter,
        private readonly RacerParserContract $racerParser,
        private readonly PreviewParserContract $previewParser,
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

        $scraperFormat = '%s/owpc/pc/race/beforeinfo?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, self::BASE_URL, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = ($httpBrowser ?? $this->browser->create())->request('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, self::BASE_XPATH);

        $baseLevel = 0;
        if ($this->filter->byXPath($scraper, $levelXPath) !== null) {
            $baseLevel = 1;
        }

        $windSpeedFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[3]/div/span[2]';
        $windSpeedXPath = sprintf($windSpeedFormat, self::BASE_XPATH, $baseLevel + 5);
        $windSpeedSource = $this->filter->byXPath($scraper, $windSpeedXPath);
        $windSpeed = $this->previewParser->parseWindSpeed($windSpeedSource);

        $windDirectionFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[4]/p';
        $windDirectionXPath = sprintf($windDirectionFormat, self::BASE_XPATH, $baseLevel + 5);
        $windDirectionSource = $this->filter->byXPathAsPattern($scraper, $windDirectionXPath, 'class', self::WIND_DIRECTION_PATTERN);
        $windDirection = $this->previewParser->parseWindDirection($windDirectionSource);

        $waveHeightFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[6]/div/span[2]';
        $waveHeightXPath = sprintf($waveHeightFormat, self::BASE_XPATH, $baseLevel + 5);
        $waveHeightSource = $this->filter->byXPath($scraper, $waveHeightXPath);
        $waveHeight = $this->previewParser->parseWaveHeight($waveHeightSource);

        $weatherFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[2]/div/span';
        $weatherXPath = sprintf($weatherFormat, self::BASE_XPATH, $baseLevel + 5);
        $weatherSource = $this->filter->byXPath($scraper, $weatherXPath);
        $weather = $this->previewParser->parseWeather($weatherSource);

        $airTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[1]/div/span[2]';
        $airTemperatureXPath = sprintf($airTemperatureFormat, self::BASE_XPATH, $baseLevel + 5);
        $airTemperatureSource = $this->filter->byXPath($scraper, $airTemperatureXPath);
        $airTemperature = $this->previewParser->parseAirTemperature($airTemperatureSource);

        $waterTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[5]/div/span[2]';
        $waterTemperatureXPath = sprintf($waterTemperatureFormat, self::BASE_XPATH, $baseLevel + 5);
        $waterTemperatureSource = $this->filter->byXPath($scraper, $waterTemperatureXPath);
        $waterTemperature = $this->previewParser->parseWaterTemperature($waterTemperatureSource);

        $weatherAsOfFormat = '%s/div[2]/div[%d]/div[2]/div[2]/p';
        $weatherAsOfXPath = sprintf($weatherAsOfFormat, self::BASE_XPATH, $baseLevel + 5);
        $weatherAsOfSource = $this->filter->byXPath($scraper, $weatherAsOfXPath);
        $weatherAsOf = $this->previewParser->parseWeatherAsOf($weatherAsOfSource);

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
        $response += $weatherAsOf;

        $response += $this->scrapeRacers($scraper, $baseLevel);

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
        $racers = $this->scrapePreviewTable($scraper, $baseLevel);

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
     * @return array<int, array<non-empty-string, mixed>>
     */
    private function scrapePreviewTable(Crawler $scraper, int $baseLevel): array
    {
        $response = [];

        foreach (range(1, 6) as $index) {
            $entryNumberFormat = '%s/div[2]/div[%d]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[1]';
            $entryNumberXPath = sprintf($entryNumberFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $entryNumberSource = $this->filter->byXPath($scraper, $entryNumberXPath);
            $entryNumber = $this->racerParser->parseEntryNumber($entryNumberSource);

            $course = ['course_number' => $index];

            $startTimingFormat = '%s/div[2]/div[%d]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[3]';
            $startTimingXPath = sprintf($startTimingFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $startTimingSource = $this->filter->byXPath($scraper, $startTimingXPath);
            $startTiming = $this->previewParser->parseStartTiming($startTimingSource);

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
            $entryNumberFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[1]';
            $entryNumberXPath = sprintf($entryNumberFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $entryNumberSource = $this->filter->byXPath($scraper, $entryNumberXPath);
            $entryNumber = $this->racerParser->parseEntryNumber($entryNumberSource);

            $weightFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[4]';
            $weightXPath = sprintf($weightFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $weightSource = $this->filter->byXPath($scraper, $weightXPath);
            $weight = $this->previewParser->parseWeight($weightSource);

            $weightAdjustmentFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[3]/td[1]';
            $weightAdjustmentXPath = sprintf($weightAdjustmentFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $weightAdjustmentSource = $this->filter->byXPath($scraper, $weightAdjustmentXPath);
            $weightAdjustment = $this->previewParser->parseWeightAdjustment($weightAdjustmentSource);

            $exhibitionTimeFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[5]';
            $exhibitionTimeXPath = sprintf($exhibitionTimeFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $exhibitionTimeSource = $this->filter->byXPath($scraper, $exhibitionTimeXPath);
            $exhibitionTime = $this->previewParser->parseExhibitionTime($exhibitionTimeSource);

            $tiltAdjustmentFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[6]';
            $tiltAdjustmentXPath = sprintf($tiltAdjustmentFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $tiltAdjustmentSource = $this->filter->byXPath($scraper, $tiltAdjustmentXPath);
            $tiltAdjustment = $this->previewParser->parseTiltAdjustment($tiltAdjustmentSource);

            $propellerFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[7]';
            $propellerXPath = sprintf($propellerFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $propellerSource = $this->filter->byXPath($scraper, $propellerXPath);
            $propeller = $this->previewParser->parsePropeller($propellerSource);

            $partsFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[8]';
            $partsXPath = sprintf($partsFormat, self::BASE_XPATH, $baseLevel + 5, $index);
            $partsSource = $this->filter->byXPath($scraper, $partsXPath) === null
                ? null
                : $this->filter->byXPathAsList($scraper, $partsXPath . '/ul/li');
            $parts = $this->previewParser->parseParts($partsSource);

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
            $response[$entryNumberKey] += $weight;
            $response[$entryNumberKey] += $weightAdjustment;
            $response[$entryNumberKey] += $exhibitionTime;
            $response[$entryNumberKey] += $tiltAdjustment;
            $response[$entryNumberKey] += $propeller;
            $response[$entryNumberKey] += $parts;
        }

        return $response;
    }
}
