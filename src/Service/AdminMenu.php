<?php

namespace Tourze\EnvManageBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Exception\MenuException;

/**
 * 环境变量管理菜单服务
 */
#[Autoconfigure(public: true)]
final readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('系统管理')) {
            $item->addChild('系统管理');
        }

        $systemMenu = $item->getChild('系统管理');
        if (null === $systemMenu) {
            throw MenuException::systemMenuShouldExist();
        }

        // 环境变量管理菜单
        $systemMenu->addChild('环境变量管理')
            ->setUri($this->linkGenerator->getCurdListPage(Env::class))
            ->setAttribute('icon', 'fas fa-cogs')
        ;
    }
}
