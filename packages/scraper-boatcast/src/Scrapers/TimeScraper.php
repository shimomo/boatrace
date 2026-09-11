<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper\Scrapers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\BoatcastScraper\TimeScraper as TimeScraperContract;
use Boatrace\Types\Contracts\Browser\BrowserDispatcher as BrowserDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Normalizer\NormalizerDispatcher as NormalizerDispatcherContract;
use Boatrace\Types\Contracts\Tsv\TsvDispatcher as TsvDispatcherContract;
use Carbon\CarbonImmutable as Carbon;
use DateTimeInterface;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class TimeScraper implements TimeScraperContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-string
     */
    private const string BASE_URL = 'https://race.boatcast.jp';

    /**
     * @var non-empty-string
     */
    private const string SENTINEL = 'data=';

    /**
     * @var non-empty-string
     */
    private const string MEASURED_STATUS = '1';

    /**
     * @var non-empty-array<non-empty-string, non-empty-string>
     */
    private const array LABEL_KEYS = [
        '一周' => 'lap_time',
        '半周ラップ' => 'half_lap_time',
        'まわり足' => 'turn_time',
        '直線' => 'straight_time',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array RACER_KEYS = [
        'entry_number',
        'name',
        'lap_time',
        'half_lap_time',
        'turn_time',
        'straight_time',
    ];

    /**
     * @param \Boatrace\Types\Contracts\Browser\BrowserDispatcher $browser
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     * @param \Boatrace\Types\Contracts\Normalizer\NormalizerDispatcher $normalizer
     * @param \Boatrace\Types\Contracts\Tsv\TsvDispatcher $tsv
     */
    public function __construct(
        private readonly BrowserDispatcherContract $browser,
        private readonly ConverterDispatcherContract $converter,
        private readonly NormalizerDispatcherContract $normalizer,
        private readonly TsvDispatcherContract $tsv,
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

        $scraperFormat = '%s/txt/%02d/bc_oriten_%s_%02d_%02d.txt';
        $scraperUrl = sprintf(
            $scraperFormat,
            self::BASE_URL,
            $stadiumNumber,
            $date->format('Ymd'),
            $stadiumNumber,
            $raceNumber
        );

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        $response += $this->scrapeRacers($this->request($scraperUrl, $httpBrowser));

        return $response;
    }

    /**
     * @param string $url
     * @param ?\Symfony\Component\BrowserKit\HttpBrowser $httpBrowser
     * @return list<list<string>>
     */
    private function request(string $url, ?HttpBrowser $httpBrowser): array
    {
        $httpBrowser ??= $this->browser->create();
        $httpBrowser->request('GET', $url);

        $httpResponse = $httpBrowser->getInternalResponse();

        if ($httpResponse->getStatusCode() !== 200) {
            return [];
        }

        $rows = $this->tsv->parse($httpResponse->getContent());

        if (($rows[0][0] ?? null) !== self::SENTINEL) {
            return [];
        }

        if (($rows[1][0] ?? null) !== self::MEASURED_STATUS) {
            return [];
        }

        return $rows;
    }

    /**
     * @param list<list<string>> $rows
     * @return array{
     *     racers: array<int, array<non-empty-string, mixed>>,
     * }
     */
    private function scrapeRacers(array $rows): array
    {
        $keys = $this->resolveKeys($rows);

        $template = array_fill_keys(self::RACER_KEYS, null);

        $response = ['racers' => []];

        foreach (range(1, 6) as $entryNumberKey) {
            $racer = $rows[$entryNumberKey + 2] ?? [];

            $response['racers'][$entryNumberKey] = array_replace($template, [
                'entry_number' => $entryNumberKey,
                'name' => $this->resolveName($racer[1] ?? null),
            ], $this->resolveTimes($keys, $racer));
        }

        return $response;
    }

    /**
     * @param list<list<string>> $rows
     * @return array<int, non-empty-string>
     */
    private function resolveKeys(array $rows): array
    {
        $count = $this->converter->toIntStrict($rows[1][1] ?? null);

        $labels = array_slice($rows[2] ?? [], 0, max($count, 0));

        $keys = [];

        foreach ($labels as $index => $label) {
            $label = $this->normalizer->normalize(
                $this->converter->toKana($label),
                ['should_remove_all_spaces' => true]
            );

            if (is_string($label) && isset(self::LABEL_KEYS[$label])) {
                $keys[$index + 2] = self::LABEL_KEYS[$label];
            }
        }

        return $keys;
    }

    /**
     * @param array<int, non-empty-string> $keys
     * @param array<int, string> $racer
     * @return array<non-empty-string, ?float>
     */
    private function resolveTimes(array $keys, array $racer): array
    {
        $response = [];

        foreach ($keys as $index => $key) {
            $value = $racer[$index] ?? null;

            $response[$key] = is_numeric($value) ? $this->converter->toFloat($value) : null;
        }

        return $response;
    }

    /**
     * @param ?string $value
     * @return ?string
     */
    private function resolveName(?string $value): ?string
    {
        $value = $this->normalizer->normalize($this->converter->toKana($value));

        return is_string($value) && $value !== '' ? $value : null;
    }
}
