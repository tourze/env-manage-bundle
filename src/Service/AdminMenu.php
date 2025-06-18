<?php

namespace Tourze\EnvManageBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\EnvManageBundle\Entity\Env;

/**
 * 环境变量管理菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('系统管理') === null) {
            $item->addChild('系统管理');
        }

        $systemMenu = $item->getChild('系统管理');
        
        // 环境变量管理菜单
        $systemMenu->addChild('环境变量管理')
            ->setUri($this->linkGenerator->getCurdListPage(Env::class))
            ->setAttribute('icon', 'fas fa-cogs');
    }
}
