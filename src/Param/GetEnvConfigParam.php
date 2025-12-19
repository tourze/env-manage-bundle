<?php

declare(strict_types=1);

namespace Tourze\EnvManageBundle\Param;

use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

readonly class GetEnvConfigParam implements RpcParamInterface
{
    public function __construct()
    {
    }
}
