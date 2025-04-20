# 环境变量管理组件

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/env-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/env-manage-bundle)
[![Build Status](https://img.shields.io/travis/tourze/env-manage-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/env-manage-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/env-manage-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/env-manage-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/env-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/env-manage-bundle)

一个用于在数据库中管理环境变量的 Symfony 组件，支持运行时加载、同步、审计追踪。

---

## 功能特性

- 在数据库中集中管理环境变量，具备完整审计追踪
- 支持 HTTP、CLI、Worker 场景下的运行时环境变量加载
- 精细化控制：启用/禁用、同步控制、备注说明
- 所有变更均有雪花ID、用户、IP、时间戳记录
- 与 Symfony 事件、Messenger 无缝集成
- 通过 EasyAdmin 属性可扩展管理后台

## 安装说明

### 环境要求

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM >= 2.20

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

1. 执行数据库迁移，创建 `base_env` 表（详见 ENTITY_DESIGN.md）
2. 通过管理后台或 Doctrine 新增/编辑环境变量
3. 应用启动、HTTP 请求、CLI、Worker 启动时自动加载变量并注入 `$_ENV`

### 示例：获取公开环境变量

```php
/** @var \Tourze\EnvManageBundle\Service\EnvService $service */
$vars = $service->fetchPublicArray();
```

## 详细文档

- [实体设计说明](ENTITY_DESIGN.md)
- [工作流程 (Mermaid)](WORKFLOW.md)
- 所有变量均可审计、可筛选、可同步
- 高级用法和扩展请参考源码及注解

## 贡献指南

- 欢迎 Issue 和 PR！
- 遵循 PSR 代码规范
- 提交前请运行测试和 PHPStan 静态分析

## 版权和许可

MIT License. 版权所有 (c) tourze

## 更新日志

详见 [Releases](https://packagist.org/packages/tourze/env-manage-bundle#releases) 获取版本历史与升级说明。
