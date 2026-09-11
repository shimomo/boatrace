<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Parsers;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Parser\RacerParser as RacerParserContract;

/**
 * @author shimomo
 */
final class RacerParser implements RacerParserContract
{
    use RejectsUndefinedCalls;

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array ENTRY_NUMBER_KEYS = [
        'entry_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array NUMBER_KEYS = [
        'number_source',
        'number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array NAME_KEYS = [
        'name',
    ];

    /**
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     */
    public function __construct(private readonly ConverterDispatcherContract $converter)
    {
        //
    }

    /**
     * @param ?string $value
     * @return array{
     *     entry_number: ?int,
     * }
     */
    #[\Override]
    public function parseEntryNumber(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::ENTRY_NUMBER_KEYS, null);
        }

        return array_combine(self::ENTRY_NUMBER_KEYS, [
            $this->converter->toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     number_source: ?string,
     *     number: ?int,
     * }
     */
    #[\Override]
    public function parseNumber(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::NUMBER_KEYS, null);
        }

        return array_combine(self::NUMBER_KEYS, [
            $this->converter->toString($value),
            $this->converter->toInt($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     name: ?string,
     * }
     */
    #[\Override]
    public function parseName(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::NAME_KEYS, null);
        }

        $pattern = '/([\p{L}\p{M}\p{N}]+)\s+([\p{L}\p{M}\p{N}]+)/u';
        if (preg_match($pattern, $value, $matches)) {
            return array_combine(self::NAME_KEYS, [
                $this->converter->toString($matches[1] . ' ' . $matches[2]),
            ]);
        }

        $nameMap = [
            '小神野紀代子' => '小神野 紀代子',
            '堀之内紀代子' => '堀之内 紀代子',
            '大久保信一郎' => '大久保 信一郎',
            'マイケル田代' => 'マイケル 田代',
            '安河内鈴之介' => '安河内 鈴之介',
        ];

        return array_combine(self::NAME_KEYS, [
            $this->converter->toString($nameMap[$value] ?? $value),
        ]);
    }
}
