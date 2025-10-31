<?php

namespace Tourze\EnvManageBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Env::class)]
final class EnvTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的新实例。
     */
    protected function createEntity(): Env
    {
        return new Env();
    }

    /**
     * 提供属性名称和示例值，用于自动测试 getter 和 setter 方法。
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'TEST_VAR'];
        yield 'value' => ['value', 'test_value'];
        yield 'remark' => ['remark', '测试备注'];
        yield 'sync' => ['sync', true];
        yield 'valid' => ['valid', false];
        yield 'createdBy' => ['createdBy', 'test_user'];
        yield 'updatedBy' => ['updatedBy', 'test_admin'];
        yield 'createdFromIp' => ['createdFromIp', '127.0.0.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.1'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    public function testCreateEnvWithValidData(): void
    {
        $env = new Env();
        $env->setName('TEST_VAR');
        $env->setValue('test_value');
        $env->setRemark('测试备注');
        $env->setSync(true);
        $env->setValid(true);

        $this->assertSame('TEST_VAR', $env->getName());
        $this->assertSame('test_value', $env->getValue());
        $this->assertSame('测试备注', $env->getRemark());
        $this->assertTrue($env->isSync());
        $this->assertTrue($env->isValid());
    }

    public function testRetrieveApiArrayContainsExpectedKeys(): void
    {
        $env = new Env();
        $env->setName('TEST_VAR');
        $env->setValue('test_value');

        $array = $env->retrieveApiArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertSame('TEST_VAR', $array['name']);
        $this->assertSame('test_value', $array['value']);
    }

    public function testRetrieveAdminArrayContainsExpectedKeys(): void
    {
        $env = new Env();
        $env->setName('TEST_VAR');
        $env->setValue('test_value');
        $env->setRemark('测试备注');
        $env->setSync(true);
        $env->setValid(true);

        $array = $env->retrieveAdminArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('remark', $array);
        $this->assertArrayHasKey('sync', $array);
        $this->assertArrayHasKey('valid', $array);
        $this->assertSame('TEST_VAR', $array['name']);
        $this->assertSame('test_value', $array['value']);
        $this->assertSame('测试备注', $array['remark']);
        $this->assertTrue($array['sync']);
        $this->assertTrue($array['valid']);
    }

    public function testTrackableFieldsSettersAndGetters(): void
    {
        $env = new Env();
        $now = new \DateTimeImmutable();

        $env->setCreatedBy('test_user');
        $env->setUpdatedBy('test_admin');
        $env->setCreatedFromIp('127.0.0.1');
        $env->setUpdatedFromIp('192.168.1.1');
        $env->setCreateTime($now);
        $env->setUpdateTime($now);

        $this->assertSame('test_user', $env->getCreatedBy());
        $this->assertSame('test_admin', $env->getUpdatedBy());
        $this->assertSame('127.0.0.1', $env->getCreatedFromIp());
        $this->assertSame('192.168.1.1', $env->getUpdatedFromIp());
        $this->assertSame($now, $env->getCreateTime());
        $this->assertSame($now, $env->getUpdateTime());
    }
}
