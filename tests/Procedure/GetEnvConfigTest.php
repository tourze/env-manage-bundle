<?php

namespace Tourze\EnvManageBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Procedure\GetEnvConfig;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetEnvConfig::class)]
#[RunTestsInSeparateProcesses]
final class GetEnvConfigTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void        // 手动清理数据库，确保表被创建
    {
        self::cleanDatabase();
    }

    protected function createProcedure(): GetEnvConfig
    {
        return self::getService(GetEnvConfig::class);
    }

    public function testExecuteReturnsEnvServiceData(): void
    {
        // 创建测试环境变量数据
        $env1 = new Env();
        $env1->setName('TEST_PROCEDURE_ENV1');
        $env1->setValue('procedure_value1');
        $env1->setValid(true);
        $env1->setSync(true);

        $env2 = new Env();
        $env2->setName('TEST_PROCEDURE_ENV2');
        $env2->setValue('procedure_value2');
        $env2->setValid(true);
        $env2->setSync(true);

        // 保存到数据库
        $this->persistAndFlush($env1);
        $this->persistAndFlush($env2);

        $procedure = $this->createProcedure();
        $result = $procedure->execute();

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsArray($result);
        $this->assertArrayHasKey('envs', $result);
        $this->assertIsArray($result['envs']);
    }

    public function testGetCacheKeyReturnsExpectedKey(): void
    {
        $procedure = $this->createProcedure();
        $request = new JsonRpcRequest();
        $cacheKey = $procedure->getCacheKey($request);

        $this->assertSame('GetEnvConfig_cache', $cacheKey);
    }

    public function testGetCacheDurationReturns24Hours(): void
    {
        $procedure = $this->createProcedure();
        $request = new JsonRpcRequest();
        $duration = $procedure->getCacheDuration($request);

        $this->assertSame(60 * 60 * 24, $duration);
    }
}
