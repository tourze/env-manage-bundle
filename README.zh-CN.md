# 环境变量管理组件

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)  
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)
[![License](https://img.shields.io/packagist/l/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)  
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](https://codecov.io/gh/tourze/php-monorepo)

一个用于在数据库中管理环境变量的 Symfony 组件，支持运行时加载、同步和
审计追踪。

该组件提供了一种安全且灵活的方式，通过数据库存储的环境变量来管理应用程序
配置，并自动为 HTTP 请求、CLI 命令和消息队列工作器加载配置。

## 目录

- [功能特性](#功能特性)
- [环境要求](#环境要求)
- [安装说明](#安装说明)
  - [Composer 安装](#composer-安装)
  - [启用组件](#启用组件)
- [快速开始](#快速开始)
- [配置说明](#配置说明)
  - [安全配置](#安全配置)
  - [管理界面](#管理界面)
  - [缓存机制](#缓存机制)
- [事件和扩展点](#事件和扩展点)
- [高级用法](#高级用法)
- [详细文档](#详细文档)
- [测试](#测试)
- [贡献指南](#贡献指南)
- [安全](#安全)
- [致谢](#致谢)
- [版权和许可](#版权和许可)
- [更新日志](#更新日志)

---

## 功能特性

- **数据库驱动配置**：在数据库中存储和管理环境变量
- **运行时加载**：自动为 HTTP 请求、CLI 命令和消息队列工作器加载变量
- **安全优先**：内置对危险环境变量的保护（LD_PRELOAD、APP_* 等）
- **完整审计追踪**：使用雪花 ID、用户、IP 和时间戳跟踪所有变更
- **精细化控制**：启用/禁用单个变量、添加备注、控制同步
- **缓存层**：高性能缓存，变更时自动失效
- **管理界面**：即用型 EasyAdmin CRUD 控制器
- **JSON-RPC 支持**：内置获取公共配置的过程
- **Twig 集成**：在模板中直接访问环境变量

## 环境要求

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM >= 2.20

## 安装说明

### Composer 安装

```bash
composer require tourze/env-manage-bundle
```

### 启用组件

通常由 Symfony Flex 自动注册。如需手动添加到 `config/bundles.php`：

```php
Tourze\EnvManageBundle\EnvManageBundle::class => ['all' => true],
```

## 快速开始

### 1. 创建数据库表

```bash
# 生成迁移文件
php bin/console make:migration

# 运行迁移创建 base_env 表
php bin/console doctrine:migrations:migrate
```

## 2. 添加环境变量

```php
use Tourze\EnvManageBundle\Entity\Env;
use Doctrine\ORM\EntityManagerInterface;

$env = new Env();
$env->setName('API_ENDPOINT');
$env->setValue('https://api.example.com');
$env->setValid(true);
$env->setSync(false);
$env->setRemark('外部 API 端点');

$entityManager->persist($env);
$entityManager->flush();
```

## 3. 访问变量

```php
// 在服务中
$apiEndpoint = $_ENV['API_ENDPOINT'] ?? 'default';

// 使用服务
/** @var \Tourze\EnvManageBundle\Service\EnvService $envService */
$publicVars = $envService->fetchPublicArray();

// 在 Twig 模板中
{{ env_value('API_ENDPOINT') }}
```

## 配置说明

## 安全配置

组件自动阻止危险的环境变量：
- `LD_PRELOAD` - 防止注入攻击
- `APP_*` - 保护 Symfony 核心配置
- `DATABASE_*` - 防止数据库凭据覆盖
- `REDIS_*` - 保护缓存配置
- `JWT_*` - 保护认证令牌
- `MESSENGER_*` - 保护消息队列配置
- `LOCK_*` - 防止锁机制篡改

## 管理界面

添加到 EasyAdmin 仪表板：

```yaml
# config/packages/easy_admin.yaml
easy_admin:
    entities:
        Env:
            class: Tourze\EnvManageBundle\Entity\Env
            controller: Tourze\EnvManageBundle\Controller\Admin\EnvCrudController
```

## 缓存机制

环境变量缓存 24 小时，以下情况自动失效：
- 任何环境变量被创建、更新或删除
- 手动清除缓存
- 应用程序部署

## 事件和扩展点

### 事件监听器

组件监听以下事件：
- `KernelEvents::REQUEST` - 为 HTTP 请求加载变量
- `WorkerStartedEvent` - 为消息队列工作器加载变量
- `ConsoleEvents::COMMAND` - 为 CLI 命令加载变量（缓存命令除外）

### 实体事件

Doctrine 实体监听器自动清除缓存：
- `postPersist` - 创建新变量后
- `postUpdate` - 更新变量后
- `postRemove` - 删除变量后

## 高级用法

### 自定义环境服务

```php
use Tourze\EnvManageBundle\Service\EnvService;

class MyEnvService implements EnvService
{
    public function fetchPublicArray(): array
    {
        // 公共变量的自定义逻辑
    }
}
```

### JSON-RPC 集成

```php
// 通过 JSON-RPC 暴露环境变量
$procedure = new GetEnvConfig($envService);
$result = $procedure->execute();
```

## 详细文档

- [实体设计](ENTITY_DESIGN.md) - 数据库架构和实体详情
- [工作流程](WORKFLOW.md) - 可视化工作流程图
- [API 参考](docs/api.md) - 完整的 API 文档

## 测试

```bash
# 运行测试
./vendor/bin/phpunit packages/env-manage-bundle/tests

# 运行静态分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/env-manage-bundle
```

## 贡献指南

1. Fork 仓库
2. 创建功能分支 (`git checkout -b feature/amazing-feature`)
3. 提交更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 创建 Pull Request

### 开发标准

- 遵循 PSR-12 编码标准
- 为新功能编写测试
- 提交前运行 PHPStan（level 5）
- 根据需要更新文档
- 添加有意义的提交信息

## 安全

如果您发现任何安全相关问题，请发送电子邮件至 security@tourze.com，而不是使用问题跟踪器。

## 致谢

- [Tourze 团队](https://github.com/tourze)
- [所有贡献者](../../contributors)

## 版权和许可

MIT License. 版权所有 (c) tourze。请参阅 [许可文件](LICENSE) 了解更多信息。

## 更新日志

详见 [Releases](https://packagist.org/packages/tourze/env-manage-bundle#releases) 获取版本历史与升级说明。
