<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Timing;

use Boatrace\Support\Timing\Timing;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TimingTest extends TestCase
{
    /**
     * @param list<string>|string $arguments
     * @param array<non-empty-string, array{dur: ?float, desc: ?string}> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TimingDataProvider::class, 'parseProvider')]
    public function parseReturnsMetrics(array|string $arguments, array $expected): void
    {
        $this->assertSame($expected, Timing::parse($arguments)->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function parseReturnsEmptyArrayWhenGivenNull(): void
    {
        $this->assertSame([], Timing::parse(null)->getValue());
    }
}
