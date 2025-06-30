<?php

namespace Tourze\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\EnvManageBundle\Entity\Env;

class EnvTest extends TestCase
{
    public function testCreateEnv_withValidData(): void
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

    public function testRetrieveApiArray_containsExpectedKeys(): void
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

    public function testRetrieveAdminArray_containsExpectedKeys(): void
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

    public function testTrackableFields_settersAndGetters(): void
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
