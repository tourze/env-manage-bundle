<?php

namespace Tourze\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Service\AdminMenu;

class AdminMenuTest extends TestCase
{
    private LinkGeneratorInterface|MockObject $linkGenerator;
    private ItemInterface|MockObject $menuItem;
    private ItemInterface|MockObject $systemMenuItem;

    private AdminMenu $adminMenu;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->menuItem = $this->createMock(ItemInterface::class);
        $this->systemMenuItem = $this->createMock(ItemInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function testInvoke_createsSystemManagementMenu_whenNotExists(): void
    {
        $this->menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('系统管理')
            ->willReturnOnConsecutiveCalls(null, $this->systemMenuItem);

        $this->menuItem->expects($this->once())
            ->method('addChild')
            ->with('系统管理');

        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Env::class)
            ->willReturn('/admin/env');

        $envMenuItem = $this->createMock(ItemInterface::class);
        $envMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/env')
            ->willReturnSelf();

        $envMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-cogs');

        $this->systemMenuItem->expects($this->once())
            ->method('addChild')
            ->with('环境变量管理')
            ->willReturn($envMenuItem);

        ($this->adminMenu)($this->menuItem);
    }

        public function testInvoke_usesExistingSystemManagementMenu_whenExists(): void
    {
        $this->menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('系统管理')
            ->willReturn($this->systemMenuItem);
            
        $this->menuItem->expects($this->never())
            ->method('addChild');

        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Env::class)
            ->willReturn('/admin/env');

        $envMenuItem = $this->createMock(ItemInterface::class);
        $envMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/env')
            ->willReturnSelf();

        $envMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-cogs');

        $this->systemMenuItem->expects($this->once())
            ->method('addChild')
            ->with('环境变量管理')
            ->willReturn($envMenuItem);

        ($this->adminMenu)($this->menuItem);
    }
}
