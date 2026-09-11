<?php

declare(strict_types=1);

namespace Boatrace\Types\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Part: int
{
    case ピストン = 1;
    case ピストンリング = 2;
    case 電気一式 = 3;
    case キャブレター = 4;
    case シリンダ = 5;
    case クランクシャフト = 6;
    case ギヤケース = 7;
    case キャリアボデー = 8;

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
            Part::ピストン => 'ピストン',
            Part::ピストンリング => 'リング',
            Part::電気一式 => '電気',
            Part::キャブレター => 'キャブ',
            Part::シリンダ => 'シリンダ',
            Part::クランクシャフト => 'シャフト',
            Part::ギヤケース => 'ギヤ',
            Part::キャリアボデー => 'キャリボ',
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
     *     number: int<1, 8>,
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
