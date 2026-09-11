<?php

declare(strict_types=1);

namespace Boatrace\Types\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Grade: int
{
    case SG = 1;
    case G1 = 2;
    case G2 = 3;
    case G3 = 4;
    case OPEN = 5;
    case PG1 = 6;

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
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
     * @return list<array{
     *     number: int<1, 6>,
     *     name: non-empty-string,
     * }>
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'number' => $case->value,
            'name' => $case->name(),
        ], self::cases());
    }
}
