<?php

declare(strict_types=1);

namespace Tourze\EnvManageBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EnvManageBundle\Param\GetEnvConfigParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(GetEnvConfigParam::class)]
final class GetEnvConfigParamTest extends TestCase
{
    public function testParamCanBeConstructed(): void
    {
        $param = new GetEnvConfigParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }
}
