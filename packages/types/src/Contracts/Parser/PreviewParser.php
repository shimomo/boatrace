<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Parser;

/**
 * @author shimomo
 */
interface PreviewParser extends Parser
{
    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWindSpeed(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWindDirection(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWaveHeight(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWeather(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseAirTemperature(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWaterTemperature(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWeatherAsOf(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseStartTiming(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWeight(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseWeightAdjustment(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseExhibitionTime(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseTiltAdjustment(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parsePropeller(?string $value): array;

    /**
     * @param ?list<string> $values
     * @return array<non-empty-string, mixed>
     */
    public function parseParts(?array $values): array;
}
