<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Parser;

/**
 * @author shimomo
 */
interface RacerParser extends Parser
{
    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseEntryNumber(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseNumber(?string $value): array;

    /**
     * @param ?string $value
     * @return array<non-empty-string, mixed>
     */
    public function parseName(?string $value): array;
}
