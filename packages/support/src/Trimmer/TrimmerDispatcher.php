<?php

declare(strict_types=1);

namespace Boatrace\Support\Trimmer;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Trimmer\TrimmerDispatcher as TrimmerDispatcherContract;

/**
 * @author shimomo
 */
final class TrimmerDispatcher implements TrimmerDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param ?string $value
     * @param ?string $characters
     * @param ?string $encoding
     * @return ?string
     */
    #[\Override]
    public function trimOrNull(?string $value, ?string $characters = null, ?string $encoding = null): ?string
    {
        return $value !== null ? mb_trim($value, $characters, $encoding) : null;
    }

    /**
     * @param ?string $value
     * @param ?string $characters
     * @param ?string $encoding
     * @return ?string
     */
    #[\Override]
    public function ltrimOrNull(?string $value, ?string $characters = null, ?string $encoding = null): ?string
    {
        return $value !== null ? mb_ltrim($value, $characters, $encoding) : null;
    }

    /**
     * @param ?string $value
     * @param ?string $characters
     * @param ?string $encoding
     * @return ?string
     */
    #[\Override]
    public function rtrimOrNull(?string $value, ?string $characters = null, ?string $encoding = null): ?string
    {
        return $value !== null ? mb_rtrim($value, $characters, $encoding) : null;
    }
}
