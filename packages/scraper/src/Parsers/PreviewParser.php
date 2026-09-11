<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Parsers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Parser\PreviewParser as PreviewParserContract;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;
use Boatrace\Types\Enums\Part;
use Boatrace\Types\Enums\Weather;
use Boatrace\Types\Enums\WindDirection;

/**
 * @author shimomo
 */
final class PreviewParser implements PreviewParserContract
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
    private const array START_TIMING_KEYS = [
        'start_timing_source',
        'start_timing',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WEIGHT_KEYS = [
        'weight_source',
        'weight',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WEIGHT_ADJUSTMENT_KEYS = [
        'weight_adjustment_source',
        'weight_adjustment',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array EXHIBITION_TIME_KEYS = [
        'exhibition_time_source',
        'exhibition_time',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array TILT_ADJUSTMENT_KEYS = [
        'tilt_adjustment_source',
        'tilt_adjustment',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array PROPELLER_KEYS = [
        'propeller',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array PARTS_KEYS = [
        'parts',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array WEATHER_AS_OF_KEYS = [
        'weather_as_of_source',
        'weather_as_of_race_number',
        'weather_as_of_time',
    ];

    /**
     * @var non-empty-string
     */
    private const string PART_PATTERN = '/^(.+?)×(\d+)$/u';

    /**
     * @var non-empty-string
     */
    private const string WEATHER_AS_OF_CAPTION_PATTERN = '/^水面気象情報/u';

    /**
     * @var non-empty-string
     */
    private const string WEATHER_AS_OF_RACE_PATTERN = '/^(\d{1,2})R時点$/u';

    /**
     * @var non-empty-string
     */
    private const string WEATHER_AS_OF_TIME_PATTERN = '/^(\d{1,2}:\d{2})現在$/u';

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
     *     weather_as_of_source: ?string,
     *     weather_as_of_race_number: ?int,
     *     weather_as_of_time: ?string,
     * }
     */
    #[\Override]
    public function parseWeatherAsOf(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WEATHER_AS_OF_KEYS, null);
        }

        $source = $this->trimmer->trimOrNull(
            preg_replace(self::WEATHER_AS_OF_CAPTION_PATTERN, '', $value) ?? $value
        );

        if ($source === null || $source === '') {
            return array_fill_keys(self::WEATHER_AS_OF_KEYS, null);
        }

        return array_combine(self::WEATHER_AS_OF_KEYS, [
            $this->converter->toString($source),
            preg_match(self::WEATHER_AS_OF_RACE_PATTERN, $source, $matches)
                ? $this->converter->toInt($matches[1])
                : null,
            preg_match(self::WEATHER_AS_OF_TIME_PATTERN, $source, $matches)
                ? $this->converter->toString($matches[1])
                : null,
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
     *     weight_source: ?string,
     *     weight: ?float,
     * }
     */
    #[\Override]
    public function parseWeight(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WEIGHT_KEYS, null);
        }

        return array_combine(self::WEIGHT_KEYS, [
            $this->converter->toString($value),
            $this->converter->toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     weight_adjustment_source: ?string,
     *     weight_adjustment: ?float,
     * }
     */
    #[\Override]
    public function parseWeightAdjustment(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::WEIGHT_ADJUSTMENT_KEYS, null);
        }

        return array_combine(self::WEIGHT_ADJUSTMENT_KEYS, [
            $this->converter->toString($value),
            $this->converter->toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     exhibition_time_source: ?string,
     *     exhibition_time: ?float,
     * }
     */
    #[\Override]
    public function parseExhibitionTime(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::EXHIBITION_TIME_KEYS, null);
        }

        return array_combine(self::EXHIBITION_TIME_KEYS, [
            $this->converter->toString($value),
            $this->converter->toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     tilt_adjustment_source: ?string,
     *     tilt_adjustment: ?float,
     * }
     */
    #[\Override]
    public function parseTiltAdjustment(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::TILT_ADJUSTMENT_KEYS, null);
        }

        return array_combine(self::TILT_ADJUSTMENT_KEYS, [
            $this->converter->toString($value),
            $this->converter->toFloat($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     propeller: ?string,
     * }
     */
    #[\Override]
    public function parsePropeller(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::PROPELLER_KEYS, null);
        }

        return array_combine(self::PROPELLER_KEYS, [
            $this->converter->toString($value),
        ]);
    }

    /**
     * @param ?list<string> $values
     * @return array{
     *     parts: ?list<array{
     *         number_source: ?string,
     *         number: ?int,
     *         quantity: ?int,
     *     }>,
     * }
     */
    #[\Override]
    public function parseParts(?array $values): array
    {
        if ($values === null) {
            return array_fill_keys(self::PARTS_KEYS, null);
        }

        $parts = [];

        foreach ($values as $value) {
            $value = $this->trimmer->trimOrNull($value);

            if ($value === null || $value === '') {
                continue;
            }

            $shortName = $value;
            $quantity = null;

            if (preg_match(self::PART_PATTERN, $value, $matches)) {
                $shortName = $matches[1];
                $quantity = $matches[2];
            }

            $parts[] = [
                'number_source' => $this->converter->toString($shortName),
                'number' => $this->converter->toInt(
                    $this->converter->toEnumOrNull(fn() => Part::fromShortName($shortName))?->value
                ),
                'quantity' => $this->converter->toInt($quantity),
            ];
        }

        return array_combine(self::PARTS_KEYS, [$parts]);
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
