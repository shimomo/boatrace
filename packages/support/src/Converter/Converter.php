<?php

declare(strict_types=1);

namespace Boatrace\Support\Converter;

use Boatrace\Core\Concerns\ResolvesFacadeResponse;
use Boatrace\Core\CoreContainer;
use Boatrace\Types\Contracts\Converter\Converter as ConverterContract;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Converter\ConverterResponse as ConverterResponseContract;

/**
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toInt(int|float|string|null $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toIntStrict(int|float|string|null $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toIntOrReturn(int|float|string|null $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toFloat(int|float|string|null $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toFloatStrict(int|float|string $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toFloatOrReturn(int|float|string|null $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toString(int|float|string|null $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toStringStrict(int|float|string $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toNull(int|float|string|null $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toKana(?string $value, string $mode = 'KVas')
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toDayNumber(?string $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toCamelCase(string $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toCamelCaseKeys(array $value)
 * @method static \Boatrace\Types\Contracts\Converter\ConverterResponse
 *     toEnumOrNull(callable $resolver)
 * @author shimomo
 */
final class Converter implements ConverterContract
{
    use ResolvesFacadeResponse;

    /**
     * @param \Boatrace\Types\Contracts\Converter\ConverterDispatcher $converter
     */
    public function __construct(private readonly ConverterDispatcherContract $converter)
    {
        //
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Converter\ConverterResponse
     * @throws \LogicException
     */
    public function __call(string $name, array $arguments): ConverterResponseContract
    {
        return self::toResponse(ConverterResponseContract::class, $this->converter->$name(...$arguments));
    }

    /**
     * @param non-empty-string $name
     * @param list<mixed> $arguments
     * @return \Boatrace\Types\Contracts\Converter\ConverterResponse
     * @throws \LogicException
     */
    public static function __callStatic(string $name, array $arguments): ConverterResponseContract
    {
        return self::assertResponse(
            ConverterResponseContract::class,
            CoreContainer::getInstance(ConverterContract::class)->$name(...$arguments),
        );
    }
}
