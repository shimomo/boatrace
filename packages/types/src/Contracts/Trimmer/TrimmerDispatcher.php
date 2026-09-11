<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Trimmer;

/**
 * @author shimomo
 */
interface TrimmerDispatcher extends Trimmer
{
    /**
     * @param ?string $value
     * @param ?string $characters
     * @return ?string
     */
    public function trimOrNull(?string $value, ?string $characters = null): ?string;

    /**
     * @param ?string $value
     * @param ?string $characters
     * @return ?string
     */
    public function ltrimOrNull(?string $value, ?string $characters = null): ?string;

    /**
     * @param ?string $value
     * @param ?string $characters
     * @return ?string
     */
    public function rtrimOrNull(?string $value, ?string $characters = null): ?string;
}
