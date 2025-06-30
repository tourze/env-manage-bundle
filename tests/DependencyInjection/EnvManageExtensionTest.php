<?php

namespace Tourze\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\EnvManageBundle\DependencyInjection\EnvManageExtension;

class EnvManageExtensionTest extends TestCase
{
    public function testLoad_loadsServicesConfiguration(): void
    {
        $container = new ContainerBuilder();
        $extension = new EnvManageExtension();

        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('Tourze\EnvManageBundle\Service\EnvServiceImpl'));
        $this->assertTrue($container->hasDefinition('Tourze\EnvManageBundle\Service\AdminMenu'));
        $this->assertTrue($container->hasDefinition('Tourze\EnvManageBundle\Twig\EnvExtension'));
    }

    public function testLoad_withEmptyConfigs_doesNotThrowException(): void
    {
        $container = new ContainerBuilder();
        $extension = new EnvManageExtension();

        $this->expectNotToPerformAssertions();
        $extension->load([], $container);
    }
}
