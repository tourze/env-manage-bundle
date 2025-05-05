<?php

namespace Tourze\EnvManageBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\EnvManageBundle\Repository\EnvRepository;
use Tourze\EnvManageBundle\Service\EnvService;
use Tourze\EnvManageBundle\Service\EnvServiceImpl;

class EnvManageIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testEnvServiceImplIsInstantiable(): void
    {
        $envRepository = $this->createMock(EnvRepository::class);
        $service = new EnvServiceImpl($envRepository);
        
        $this->assertInstanceOf(EnvService::class, $service);
    }
    
    public function testKernelHasRequiredBundles(): void
    {
        $kernel = self::$kernel;
        $bundles = $kernel->getBundles();
        
        $this->assertArrayHasKey('EnvManageBundle', $bundles);
        $this->assertArrayHasKey('FrameworkBundle', $bundles);
    }
    
    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }
}
