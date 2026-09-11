<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Parsers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Parser\ResultParser as ResultParserContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use Boatrace\Types\Enums\Place;
use Boatrace\Types\Enums\Technique;
use Boatrace\Types\Enums\Weather;
use Boatrace\Types\Enums\WindDirection;

/**
 * @author shimomo
 */
final class ResultParser implements ResultParserContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WIND_SPEED_KEYS = [
        'wind_speed_source',
        'wind_speed',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WIND_DIRECTION_NUMBER_KEYS = [
        'wind_direction_number_source',
        'wind_direction_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WAVE_HEIGHT_KEYS = [
        'wave_height_source',
        'wave_height',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WEATHER_KEYS = [
        'weather_number_source',
        'weather_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array AIR_TEMPERATURE_KEYS = [
        'air_temperature_source',
        'air_temperature',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WATER_TEMPERATURE_KEYS = [
        'water_temperature_source',
        'water_temperature',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array TECHNIQUE_KEYS = [
        'technique_number_source',
        'technique_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array REMARKS_KEYS = [
        'remarks',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array START_TIMING_KEYS = [
        'start_timing_source',
        'start_timing',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array PLACE_KEYS = [
        'place_number_source',
        'place_number',
    ];

    /**
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     * @param \Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher $trimmer
     */
    public function __construct(
        private readonly ConverterDispatcherContract $converter,
        private readonly TrimmerDispatcherContract $trimmer,
    ) {
        //
    }

    /**
     * @param ?string $value
     * @return array{
     *     wind_speed_source: ?string,
     *     wind_speed: ?int,
     * }
     */
    #[\Override]
    public function parseWindSpeed(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WIND_SPEED_KEYS, null);
        }

        return array_combine(self::WIND_SPEED_KEYS, [
            $this->converter->toString($value),
            $this->converter->toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     wind_direction_number_source: ?string,
     *     wind_direction_number: ?int,
     * }
     */
    #[\Override]
    public function parseWindDirection(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WIND_DIRECTION_NUMBER_KEYS, null);
        }

        return array_combine(self::WIND_DIRECTION_NUMBER_KEYS, [
            $this->converter->toString(
                $this->converter->toEnumOrNull(fn() => WindDirection::fromValue($this->converter->toIntStrict($value)))?->name
            ),
            $this->converter->toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     wave_height_source: ?string,
     *     wave_height: ?int,
     * }
     */
    #[\Override]
    public function parseWaveHeight(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WAVE_HEIGHT_KEYS, null);
        }

        return array_combine(self::WAVE_HEIGHT_KEYS, [
            $this->converter->toString($value),
            $this->converter->toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     weather_number_source: ?string,
     *     weather_number: ?int,
     * }
     */
    #[\Override]
    public function parseWeather(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WEATHER_KEYS, null);
        }

        return array_combine(self::WEATHER_KEYS, [
            $this->converter->toString($value),
            $this->converter->toInt($this->converter->toEnumOrNull(fn() => Weather::fromName($value))?->value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     air_temperature_source: ?string,
     *     air_temperature: ?float,
     * }
     */
    #[\Override]
    public function parseAirTemperature(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::AIR_TEMPERATURE_KEYS, null);
        }

        return array_combine(self::AIR_TEMPERATURE_KEYS, [
            $this->converter->toString($value),
            $this->converter->toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     water_temperature_source: ?string,
     *     water_temperature: ?float,
     * }
     */
    #[\Override]
    public function parseWaterTemperature(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WATER_TEMPERATURE_KEYS, null);
        }

        return array_combine(self::WATER_TEMPERATURE_KEYS, [
            $this->converter->toString($value),
            $this->converter->toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     technique_number_source: ?string,
     *     technique_number: ?int,
     * }
     */
    #[\Override]
    public function parseTechnique(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::TECHNIQUE_KEYS, null);
        }

        return array_combine(self::TECHNIQUE_KEYS, [
            $this->converter->toString($value),
            $this->converter->toInt($this->converter->toEnumOrNull(fn() => Technique::fromName($value))?->value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     remarks: ?string,
     * }
     */
    #[\Override]
    public function parseRemarks(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::REMARKS_KEYS, null);
        }

        return array_combine(self::REMARKS_KEYS, [
            $this->converter->toString($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     start_timing_source: ?string,
     *     start_timing: ?float,
     * }
     */
    #[\Override]
    public function parseStartTiming(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::START_TIMING_KEYS, null);
        }

        if (!preg_match('/(L|F\.\d{2}|0?\.\d{2})/u', $value)) {
            return array_combine(self::START_TIMING_KEYS, [
                $this->converter->toString($value),
                $this->converter->toNull($value),
            ]);
        }

        $values = $this->splitAndTrim($value, ' ');

        return array_combine(self::START_TIMING_KEYS, [
            $this->converter->toString(array_shift($values)),
            match (substr($value, 0, 1)) {
                'L' => $this->converter->toNull($value),
                'F' => $this->converter->toFloat('-0' . mb_ltrim($value, 'F')),
                default => $this->converter->toFloat('0' . $value),
            },
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     place_number_source: ?string,
     *     place_number: ?int,
     * }
     */
    #[\Override]
    public function parsePlace(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::PLACE_KEYS, null);
        }

        return array_combine(self::PLACE_KEYS, [
            $this->converter->toString($value),
            $this->converter->toInt($this->converter->toEnumOrNull(fn() => Place::fromShortName($value))?->value),
        ]);
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $delimiter
     * @return list<?string>
     */
    private function splitAndTrim(string $value, string $delimiter = '/'): array
    {
        return array_map(
            fn(string $item): ?string => $this->trimmer->trimOrNull($item),
            explode($delimiter, $value)
        );
    }
}
