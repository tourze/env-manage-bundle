<?php

namespace Tourze\EnvManageBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Tourze\EnvManageBundle\EnvManageBundle;

class IntegrationTestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new EnvManageBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'test' => true,
                'secret' => 'test',
                'session' => [
                    'storage_factory_id' => 'session.storage.factory.mock_file',
                ],
                'router' => [
                    'resource' => 'kernel::loadRoutes',
                    'type' => 'service',
                    'utf8' => true,
                ],
            ]);

            $container->loadFromExtension('doctrine', [
                'dbal' => [
                    'driver' => 'pdo_sqlite',
                    'path' => '%kernel.project_dir%/var/data.db',
                    'memory' => true,
                ],
                'orm' => [
                    'auto_generate_proxy_classes' => true,
                    'auto_mapping' => true,
                    'mappings' => [
                        'EnvManageBundle' => [
                            'is_bundle' => true,
                            'type' => 'attribute',
                            'dir' => 'Entity',
                            'prefix' => 'Tourze\EnvManageBundle\Entity',
                            'alias' => 'EnvManageBundle',
                        ],
                    ],
                ],
            ]);
            
            // 在测试环境中禁用一些可能影响测试的服务
            $container->loadFromExtension('env_manage', []);
        });
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/env_manage_bundle_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/env_manage_bundle_logs';
    }
    
    public function getProjectDir(): string
    {
        return dirname(__DIR__, 2);
    }
}
