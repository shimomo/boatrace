<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Normalizer;

use BadMethodCallException;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Normalizer\NormalizerDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class NormalizerDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Normalizer\NormalizerDispatcher
     */
    protected NormalizerDispatcher $normalizer;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->normalizer = new NormalizerDispatcher(
            new ConverterDispatcher(new TrimmerDispatcher())
        );
    }

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
        $this->assertSame($expected, $this->normalizer->normalize($arguments));
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
        $this->assertSame($expected, $this->normalizer->normalize($arguments, $options));
    }

    /**
     * @return void
     */
    #[Test]
    public function normalizeReturnsNormalizedArrayRecursively(): void
    {
        $this->assertSame(
            ['name' => '競艇 レース', 'racers' => ['number' => 1, 'win_rate' => 5.5]],
            $this->normalizer->normalize([
                'name' => "競艇\nレース",
                'racers' => ['number' => '1', 'win_rate' => '5.5'],
            ])
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Normalizer\NormalizerDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->normalizer->ghost();
    }
}
