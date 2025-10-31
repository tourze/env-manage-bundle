<?php

namespace Tourze\EnvManageBundle\Exception;

final class MenuException extends \LogicException
{
    public static function systemMenuShouldExist(): self
    {
        return new self('系统管理菜单应该已存在');
    }
}
