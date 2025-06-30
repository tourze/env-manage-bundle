<?php

namespace Tourze\Tests\Procedure;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\EnvManageBundle\Procedure\GetEnvConfig;
use Tourze\EnvManageBundle\Service\EnvService;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;

class GetEnvConfigTest extends TestCase
{
    /**
     * @var EnvService&MockObject
     */
    private EnvService $envService;
    private GetEnvConfig $procedure;

    protected function setUp(): void
    {
        $this->envService = $this->createMock(EnvService::class);
        $this->procedure = new GetEnvConfig($this->envService);
    }

    public function testExecute_returnsEnvServiceData(): void
    {
        $expectedData = [
            ['name' => 'TEST_VAR1', 'value' => 'value1'],
            ['name' => 'TEST_VAR2', 'value' => 'value2'],
        ];

        $this->envService->expects($this->once())
            ->method('fetchPublicArray')
            ->willReturn($expectedData);

        $result = $this->procedure->execute();

        $this->assertSame($expectedData, $result);
    }

    public function testGetCacheKey_returnsExpectedKey(): void
    {
        $request = $this->createMock(JsonRpcRequest::class);

        $cacheKey = $this->procedure->getCacheKey($request);

        $this->assertSame('GetEnvConfig_cache', $cacheKey);
    }

    public function testGetCacheDuration_returns24Hours(): void
    {
        $request = $this->createMock(JsonRpcRequest::class);

        $duration = $this->procedure->getCacheDuration($request);

        $this->assertSame(60 * 60 * 24, $duration);
    }
}
