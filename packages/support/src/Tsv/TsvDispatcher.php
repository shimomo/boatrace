<?php

declare(strict_types=1);

namespace Boatrace\Support\Tsv;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Tsv\TsvDispatcher as TsvDispatcherContract;

/**
 * @author shimomo
 */
final class TsvDispatcher implements TsvDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param ?string $value
     * @return list<list<string>>
     */
    #[\Override]
    public function parse(?string $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $lines = explode("\n", str_replace("\r\n", "\n", $value));

        while ($lines !== [] && end($lines) === '') {
            array_pop($lines);
        }

        return array_map(
            fn(string $line): array => explode("\t", $line),
            $lines
        );
    }
}
