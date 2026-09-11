<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Parser;

/**
 * @author shimomo
 */
interface ResultParser extends Parser
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
    public function parseTechnique(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseRemarks(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseStartTiming(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parsePlace(?string $value): array;
}
