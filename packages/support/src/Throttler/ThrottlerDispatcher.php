<?php

declare(strict_types=1);

namespace Boatrace\Support\Throttler;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;
use ValueError;

/**
 * @author shimomo
 */
final class ThrottlerDispatcher implements ThrottlerDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @var float
     */
    private const float JITTER_MIN_RATIO = 0.5;

    /**
     * @var float
     */
    private const float JITTER_MAX_RATIO = 1.5;

    /**
     * @var ?float
     */
    private ?float $lastThrottledAt = null;

    /**
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     * @param float $minCallIntervalSeconds
     * @param bool $jitter
     * @throws \ValueError
     */
    public function __construct(
        private readonly ConverterDispatcherContract $converter,
        private float $minCallIntervalSeconds = 1.0,
        private readonly bool $jitter = true,
    ) {
        $this->setMinCallIntervalSeconds($minCallIntervalSeconds);
    }

    /**
     * @return float
     */
    #[\Override]
    public function getMinCallIntervalSeconds(): float
    {
        return $this->minCallIntervalSeconds;
    }

    /**
     * @param float $seconds
     * @return void
     * @throws \ValueError
     */
    #[\Override]
    public function setMinCallIntervalSeconds(float $seconds): void
    {
        if ($seconds < 0.0) {
            throw new ValueError(sprintf('$seconds must be 0.0 or greater, %.1f given.', $seconds));
        }

        $this->minCallIntervalSeconds = $seconds;
    }

    /**
     * @return void
     */
    #[\Override]
    public function throttle(): void
    {
        if ($this->lastThrottledAt !== null) {
            $remainingSeconds = $this->intervalSeconds() - (microtime(true) - $this->lastThrottledAt);

            if ($remainingSeconds > 0) {
                usleep($this->converter->toIntStrict($remainingSeconds * 1_000_000.0));
            }
        }

        $this->lastThrottledAt = microtime(true);
    }

    /**
     * @return float
     */
    private function intervalSeconds(): float
    {
        if (!$this->jitter || $this->minCallIntervalSeconds <= 0.0) {
            return $this->minCallIntervalSeconds;
        }

        $ratio = self::JITTER_MIN_RATIO
            + (self::JITTER_MAX_RATIO - self::JITTER_MIN_RATIO) * ((float) mt_rand() / (float) mt_getrandmax());

        return $this->minCallIntervalSeconds * $ratio;
    }
}
