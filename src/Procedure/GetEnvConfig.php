<?php

namespace Tourze\EnvManageBundle\Procedure;

use Tourze\DoctrineHelper\CacheHelper;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Service\EnvService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

#[MethodTag(name: '基础能力')]
#[MethodDoc(summary: '返回配置')]
#[MethodExpose(method: 'GetEnvConfig')]
class GetEnvConfig extends CacheableProcedure
{
    public function __construct(
        private readonly EnvService $envService,
    ) {
    }

    public function execute(): array
    {
        return $this->envService->fetchPublicArray();
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        return 'GetEnvConfig_cache';
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60 * 60 * 24;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Env::class);
    }
}
