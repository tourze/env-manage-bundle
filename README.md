# Env Manage Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/env-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/env-manage-bundle)
[![Build Status](https://img.shields.io/travis/tourze/env-manage-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/env-manage-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/env-manage-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/env-manage-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/env-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/env-manage-bundle)

A Symfony bundle for managing environment variables in the database, supporting runtime loading, synchronization, and auditing.

---

## Features

- Manage environment variables in database with full audit trail
- Runtime loading of variables for HTTP, CLI, and worker contexts
- Fine-grained control: enable/disable, sync control, remarks
- Snowflake ID, user/IP/time tracking for all changes
- Easy integration with Symfony events and Messenger
- Extensible via admin interface attributes

## Installation

### Requirements

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM >= 2.20

### Install via Composer

```bash
composer require tourze/env-manage-bundle
```

### Enable the Bundle

Usually auto-registered via Symfony Flex. If not, add to `config/bundles.php`:

```php
Tourze\EnvManageBundle\EnvManageBundle::class => ['all' => true],
```

## Quick Start

1. Run database migrations for the `base_env` table (see entity design in ENTITY_DESIGN.md)
2. Use the admin interface or Doctrine to add/edit environment variables
3. On app start, HTTP request, CLI, or worker, variables are loaded and injected into `$_ENV`

### Example: Fetching Public Variables

```php
/** @var \Tourze\EnvManageBundle\Service\EnvService $service */
$vars = $service->fetchPublicArray();
```

## Documentation

- [Entity Design](ENTITY_DESIGN.md)
- [Workflow (Mermaid)](WORKFLOW.md)
- All variables are tracked for audit and can be filtered/synchronized as needed
- See code for advanced extension points and admin attributes

## Contribution Guide

- Issues and PRs are welcome!
- Follow PSR coding standards
- Run tests and PHPStan before submitting

## License

MIT License. Copyright (c) tourze

## Changelog

See [Releases](https://packagist.org/packages/tourze/env-manage-bundle#releases) for version history and upgrade notes.
