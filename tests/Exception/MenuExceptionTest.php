<?php

namespace Tourze\EnvManageBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EnvManageBundle\Exception\MenuException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(MenuException::class)]
final class MenuExceptionTest extends AbstractExceptionTestCase
{
    public function testSystemMenuShouldExistCreatesExceptionWithCorrectMessage(): void
    {
        $exception = MenuException::systemMenuShouldExist();

        $this->assertInstanceOf(MenuException::class, $exception);
        $this->assertInstanceOf(\LogicException::class, $exception);
        $this->assertEquals('系统管理菜单应该已存在', $exception->getMessage());
    }
}
