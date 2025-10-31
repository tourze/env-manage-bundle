<?php

namespace Tourze\EnvManageBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\EnvManageBundle\Entity\Env;

/**
 * 环境变量数据初始化
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class EnvFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建系统配置相关环境变量
        $this->createSystemConfigs($manager);

        // 创建应用配置相关环境变量
        $this->createAppConfigs($manager);

        // 创建第三方服务配置
        $this->createThirdPartyConfigs($manager);

        $manager->flush();
    }

    /**
     * 创建系统配置
     */
    private function createSystemConfigs(ObjectManager $manager): void
    {
        $configs = [
            [
                'name' => 'APP_ENV',
                'value' => 'test',
                'remark' => '应用环境配置',
                'sync' => true,
                'valid' => true,
            ],
            [
                'name' => 'APP_DEBUG',
                'value' => 'true',
                'remark' => '调试模式开关',
                'sync' => true,
                'valid' => true,
            ],
            [
                'name' => 'APP_SECRET',
                'value' => 'test_secret_key_for_fixtures',
                'remark' => '应用密钥',
                'sync' => false,
                'valid' => true,
            ],
        ];

        foreach ($configs as $config) {
            $env = new Env();
            $env->setName($config['name']);
            $env->setValue($config['value']);
            $env->setRemark($config['remark']);
            $env->setSync($config['sync']);
            $env->setValid($config['valid']);

            $manager->persist($env);
        }
    }

    /**
     * 创建应用配置
     */
    private function createAppConfigs(ObjectManager $manager): void
    {
        $configs = [
            [
                'name' => 'DATABASE_URL',
                'value' => 'mysql://user:pass@localhost:3306/test_db',
                'remark' => '数据库连接配置',
                'sync' => true,
                'valid' => true,
            ],
            [
                'name' => 'REDIS_URL',
                'value' => 'redis://localhost:6379',
                'remark' => 'Redis缓存配置',
                'sync' => true,
                'valid' => true,
            ],
            [
                'name' => 'MAILER_DSN',
                'value' => 'smtp://localhost:1025',
                'remark' => '邮件服务配置',
                'sync' => true,
                'valid' => false,
            ],
        ];

        foreach ($configs as $config) {
            $env = new Env();
            $env->setName($config['name']);
            $env->setValue($config['value']);
            $env->setRemark($config['remark']);
            $env->setSync($config['sync']);
            $env->setValid($config['valid']);

            $manager->persist($env);
        }
    }

    /**
     * 创建第三方服务配置
     */
    private function createThirdPartyConfigs(ObjectManager $manager): void
    {
        $configs = [
            [
                'name' => 'AWS_ACCESS_KEY_ID',
                'value' => 'test_access_key',
                'remark' => 'AWS访问密钥ID',
                'sync' => false,
                'valid' => false,
            ],
            [
                'name' => 'AWS_SECRET_ACCESS_KEY',
                'value' => 'test_secret_key',
                'remark' => 'AWS访问密钥',
                'sync' => false,
                'valid' => false,
            ],
            [
                'name' => 'WECHAT_APP_ID',
                'value' => 'wx1234567890abcdef',
                'remark' => '微信小程序AppID',
                'sync' => true,
                'valid' => true,
            ],
            [
                'name' => 'LOG_LEVEL',
                'value' => 'debug',
                'remark' => '日志记录级别',
                'sync' => true,
                'valid' => true,
            ],
        ];

        foreach ($configs as $config) {
            $env = new Env();
            $env->setName($config['name']);
            $env->setValue($config['value']);
            $env->setRemark($config['remark']);
            $env->setSync($config['sync']);
            $env->setValid($config['valid']);

            $manager->persist($env);
        }
    }
}
