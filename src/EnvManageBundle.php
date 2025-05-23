<?php

namespace Tourze\EnvManageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class EnvManageBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineIpBundle\DoctrineIpBundle::class => ['all' => true],
            \Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class => ['all' => true],
            \Tourze\DoctrineTrackBundle\DoctrineTrackBundle::class => ['all' => true],
            \Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
            \Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class => ['all' => true],
        ];
    }
}
