<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Validator;

use BadMethodCallException;
use Boatrace\Support\Validator\ValidatorDispatcher;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

/**
 * @author shimomo
 */
final class ValidatorDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Validator\ValidatorDispatcher
     */
    protected ValidatorDispatcher $validator;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->validator = new ValidatorDispatcher();
    }

    /**
     * @param int $arguments
     * @param int $minimum
     * @param int $maximum
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ValidatorDataProvider::class, 'betweenProvider')]
    public function betweenReturnsValue(int $arguments, int $minimum, int $maximum): void
    {
        $this->assertSame($arguments, $this->validator->between($arguments, $minimum, $maximum));
    }

    /**
     * @param int $arguments
     * @param int $minimum
     * @param int $maximum
     * @return void
     */
    #[Test]
    #[DataProviderExternal(ValidatorDataProvider::class, 'betweenOutOfRangeProvider')]
    public function betweenThrowsValueErrorWhenValueIsOutOfRange(
        int $arguments,
        int $minimum,
        int $maximum
    ): void {
        $this->expectException(ValueError::class);

        $this->validator->between($arguments, $minimum, $maximum);
    }

    /**
     * @return void
     */
    #[Test]
    public function betweenThrowsValueErrorWithName(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessageIs('$stadiumNumber must be between 1 and 24, 25 given.');

        $this->validator->between(25, 1, 24, '$stadiumNumber');
    }

    /**
     * @return void
     */
    #[Test]
    public function betweenReturnsNullWhenGivenNull(): void
    {
        $this->assertNull($this->validator->between(null, 1, 24));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Validator\ValidatorDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->validator->ghost();
    }
}
