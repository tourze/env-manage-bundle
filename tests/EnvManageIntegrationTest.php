<?php

namespace Tourze\EnvManageBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Kernel;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Service\EnvService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(EnvService::class)]
#[RunTestsInSeparateProcesses]
final class EnvManageIntegrationTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试初始化逻辑
    }

    public function testEnvServiceImplIsInstantiable(): void
    {
        $service = self::getService(EnvService::class);
        $this->assertInstanceOf(EnvService::class, $service);
    }

    public function testKernelHasRequiredBundles(): void
    {
        $container = self::getContainer();
        $this->assertNotNull($container, 'Container should be initialized');

        /** @var Kernel $kernel */
        $kernel = $container->get('kernel');
        $this->assertNotNull($kernel, 'Kernel should be available in container');

        $bundles = $kernel->getBundles();

        $this->assertArrayHasKey('EnvManageBundle', $bundles);
        $this->assertArrayHasKey('FrameworkBundle', $bundles);
    }

    public function testFetchPublicArray(): void
    {
        $service = self::getService(EnvService::class);
        $result = $service->fetchPublicArray();
        $this->assertIsArray($result);
    }

    public function testFindByName(): void
    {
        $service = self::getService(EnvService::class);
        $result = $service->findByName('test_name');

        $this->assertNull($result);
    }

    public function testFindByNameAndValid(): void
    {
        $service = self::getService(EnvService::class);
        $result = $service->findByNameAndValid('test_name');

        $this->assertNull($result);
    }

    public function testSaveEnv(): void
    {
        $env = new Env();
        $env->setName('TEST_VAR');
        $env->setValue('test_value');
        $env->setValid(true);

        $service = self::getService(EnvService::class);
        $service->saveEnv($env);

        // 验证环境变量已保存
        $this->assertNotNull($env->getId());
    }
}
