<?php

namespace Tourze\EnvManageBundle\Tests\Unit\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Repository\EnvRepository;
use Tourze\EnvManageBundle\Service\EnvServiceImpl;

class EnvServiceTest extends TestCase
{
    /**
     * @var EnvRepository&MockObject
     */
    private EnvRepository $envRepository;
    private EnvServiceImpl $envService;

    protected function setUp(): void
    {
        $this->envRepository = $this->createMock(EnvRepository::class);
        $this->envService = new EnvServiceImpl($this->envRepository);
    }

    public function testFetchPublicArray_withNoEnvironmentVariables_returnsEmptyArray(): void
    {
        $this->envRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true, 'sync' => true])
            ->willReturn([]);

        $result = $this->envService->fetchPublicArray();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testFetchPublicArray_withEnvironmentVariables_returnsFormattedArray(): void
    {
        $env1 = new Env();
        $env1->setName('TEST_ENV1');
        $env1->setValue('value1');
        $env1->setValid(true);
        $env1->setSync(true);

        $env2 = new Env();
        $env2->setName('TEST_ENV2');
        $env2->setValue('value2');
        $env2->setValid(true);
        $env2->setSync(true);

        $this->envRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true, 'sync' => true])
            ->willReturn([$env1, $env2]);

        $result = $this->envService->fetchPublicArray();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('value', $result[0]);
        $this->assertEquals('TEST_ENV1', $result[0]['name']);
        $this->assertEquals('value1', $result[0]['value']);
        $this->assertArrayHasKey('name', $result[1]);
        $this->assertArrayHasKey('value', $result[1]);
        $this->assertEquals('TEST_ENV2', $result[1]['name']);
        $this->assertEquals('value2', $result[1]['value']);
    }
}
