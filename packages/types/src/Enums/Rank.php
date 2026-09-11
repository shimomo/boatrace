<?php

declare(strict_types=1);

namespace Boatrace\Types\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Rank: int
{
    case A1級 = 1;
    case A2級 = 2;
    case B1級 = 3;
    case B2級 = 4;

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function shortName(): string
    {
        return match ($this) {
            self::A1級 => 'A1',
            self::A2級 => 'A2',
            self::B1級 => 'B1',
            self::B2級 => 'B2',
        };
    }

    /**
     * @param ?string $name
     * @return ?self
     * @throws \ValueError
     */
    public static function fromName(?string $name): ?self
    {
        if ($name === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->name() === $name) {
                return $case;
            }
        }

        throw new ValueError(
            sprintf('`%s` is not a valid name for enum `%s`.', $name, self::class)
        );
    }

    /**
     * @param ?string $shortName
     * @return ?self
     * @throws \ValueError
     */
    public static function fromShortName(?string $shortName): ?self
    {
        if ($shortName === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->shortName() === $shortName) {
                return $case;
            }
        }

        throw new ValueError(
            sprintf('`%s` is not a valid short name for enum `%s`.', $shortName, self::class)
        );
    }

    /**
     * @return list<array{
     *     number: int<1, 4>,
     *     name: non-empty-string,
     *     short_name: non-empty-string,
     * }>
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'number' => $case->value,
            'name' => $case->name(),
            'short_name' => $case->shortName(),
        ], self::cases());
    }
}
