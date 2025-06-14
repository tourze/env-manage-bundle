<?php

namespace Tourze\EnvManageBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tourze\EnvManageBundle\EnvManageBundle;
use Tourze\EnvManageBundle\Repository\EnvRepository;
use Tourze\EnvManageBundle\Service\EnvService;
use Tourze\EnvManageBundle\Service\EnvServiceImpl;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

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

    protected static function createKernel(array $options = []): IntegrationTestKernel
    {
        $appendBundles = [
            FrameworkBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            EnvManageBundle::class => ['all' => true],
        ];
        
        $entityMappings = [
            'Tourze\EnvManageBundle\Entity' => __DIR__ . '/../../src/Entity',
        ];

        return new IntegrationTestKernel(
            $options['environment'] ?? 'test',
            $options['debug'] ?? true,
            $appendBundles,
            $entityMappings
        );
    }
}
