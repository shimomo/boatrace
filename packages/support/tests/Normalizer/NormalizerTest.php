<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Normalizer;

use Boatrace\Support\Normalizer\Normalizer;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class NormalizerTest extends TestCase
{
    /**
     * @param int|float|string|null $arguments
     * @param int|float|string|null $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(NormalizerDataProvider::class, 'normalizeProvider')]
    public function normalizeReturnsNormalizedValue(
        int|float|string|null $arguments,
        int|float|string|null $expected
    ): void {
        $this->assertSame($expected, Normalizer::normalize($arguments)->getValue());
    }

    /**
     * @param non-empty-string $arguments
     * @param array<non-empty-string, bool> $options
     * @param non-empty-string $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(NormalizerDataProvider::class, 'normalizeWithOptionsProvider')]
    public function normalizeReturnsNormalizedValueWithOptions(
        string $arguments,
        array $options,
        string $expected
    ): void {
        $this->assertSame($expected, Normalizer::normalize($arguments, $options)->getValue());
    }
}
