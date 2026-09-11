<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Json;

use Boatrace\Support\Json\Json;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class JsonTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function decodeReturnsArray(): void
    {
        $this->assertSame(['res_cd' => 0], Json::decode('{"res_cd":0}')->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function decodeReturnsNullWhenGivenBrokenJson(): void
    {
        $this->assertNull(Json::decode('{"res_cd":')->getValue());
    }
}
