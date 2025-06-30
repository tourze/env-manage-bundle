<?php

namespace Tourze\EnvManageBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIpBundle\DoctrineIpBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineTrackBundle\DoctrineTrackBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\EnvManageBundle\EnvManageBundle;

class EnvManageBundleTest extends TestCase
{
    private EnvManageBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new EnvManageBundle();
    }

    public function testBundleExtension(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
        $this->assertInstanceOf(BundleDependencyInterface::class, $this->bundle);
    }

    public function testGetBundleDependencies_returnsExpectedDependencies(): void
    {
        $dependencies = EnvManageBundle::getBundleDependencies();
        
        $expectedDependencies = [
            DoctrineIpBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineTrackBundle::class => ['all' => true],
            DoctrineUserBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
        ];
        
        $this->assertSame($expectedDependencies, $dependencies);
        $this->assertCount(5, $dependencies);
    }

    public function testGetBundleDependencies_allDependenciesAreEnabledForAllEnvironments(): void
    {
        $dependencies = EnvManageBundle::getBundleDependencies();
        
        foreach ($dependencies as $bundleClass => $config) {
            $this->assertArrayHasKey('all', $config);
            $this->assertTrue($config['all']);
        }
    }
}