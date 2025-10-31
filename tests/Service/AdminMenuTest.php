<?php

namespace Tourze\EnvManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\EnvManageBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    protected function onSetUp(): void
    {
        // 在集成测试中，服务应该从容器中获取
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testServiceIsAvailableInContainer(): void
    {
        // 验证服务可以从容器中获取
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testServiceImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(MenuProviderInterface::class, $this->adminMenu);
    }

    public function testInvokeIsCallableWithoutErrors(): void
    {
        // 简化测试：仅验证 AdminMenu 实例是可调用的
        // 由于避免使用 Mock，我们重点测试服务的基本功能而非详细的交互
        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsCallable($this->adminMenu, 'AdminMenu should be callable');
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu, 'Service should be an instance of AdminMenu');

        // 验证方法存在性（通过反射检查而不是实际调用）
        $reflection = new \ReflectionClass($this->adminMenu);
        $this->assertTrue($reflection->hasMethod('__invoke'), 'AdminMenu should have __invoke method');

        $invokeMethod = $reflection->getMethod('__invoke');
        $this->assertTrue($invokeMethod->isPublic(), '__invoke method should be public');

        $parameters = $invokeMethod->getParameters();
        $this->assertCount(1, $parameters, '__invoke should accept exactly one parameter');
        $this->assertSame('item', $parameters[0]->getName(), 'Parameter should be named "item"');
    }
}
