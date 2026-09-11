<?php

declare(strict_types=1);

namespace Boatrace\Types\Enums;

/**
 * @author shimomo
 */
enum Absence: int
{
    case 未公開 = 1;
    case 想定外 = 2;
    case 発売前 = 3;
    case 中止 = 4;

    /**
     * @var non-empty-string
     */
    public const string ON_SALE_STATUS = '1';

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param ?string $status
     * @return ?self
     */
    public static function fromStatus(?string $status): ?self
    {
        return match ($status) {
            self::ON_SALE_STATUS => null,
            '0', '2', '' => self::発売前,
            '3' => self::中止,
            default => self::想定外,
        };
    }

    /**
     * @return list<array{
     *     number: int<1, 4>,
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
