<?php

namespace Tourze\EnvManageBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\EnvManageBundle\DependencyInjection\EnvManageExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(EnvManageExtension::class)]
final class EnvManageExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testLoadLoadsServicesConfiguration(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new EnvManageExtension();

        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('Tourze\EnvManageBundle\Service\EnvServiceImpl'));
        $this->assertTrue($container->hasDefinition('Tourze\EnvManageBundle\Service\AdminMenu'));
        $this->assertTrue($container->hasDefinition('Tourze\EnvManageBundle\Twig\EnvExtension'));
    }

    public function testLoadWithEmptyConfigsDoesNotThrowException(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');
        $extension = new EnvManageExtension();

        $this->expectNotToPerformAssertions();
        $extension->load([], $container);
    }
}
