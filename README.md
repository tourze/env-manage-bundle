# Env Manage Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)  
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)
[![License](https://img.shields.io/packagist/l/tourze/env-manage-bundle.svg)](https://packagist.org/packages/tourze/env-manage-bundle)  
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](https://codecov.io/gh/tourze/php-monorepo)

A Symfony bundle for managing environment variables in the database, supporting runtime
loading, synchronization, and auditing.

This bundle provides a secure and flexible way to manage application configuration through
database-stored environment variables, with automatic loading for HTTP requests, CLI
commands, and message workers.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Install via Composer](#install-via-composer)
  - [Enable the Bundle](#enable-the-bundle)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
  - [Security Configuration](#security-configuration)
  - [Admin Interface](#admin-interface)
  - [Caching](#caching)
- [Events and Extension Points](#events-and-extension-points)
- [Advanced Usage](#advanced-usage)
- [Documentation](#documentation)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)
- [Changelog](#changelog)

---

## Features

- **Database-driven Configuration**: Store and manage environment variables in the database
- **Runtime Loading**: Automatically load variables for HTTP requests, CLI commands, and message workers
- **Security First**: Built-in protection against dangerous environment variables (LD_PRELOAD, APP_*, etc.)
- **Full Audit Trail**: Track all changes with Snowflake IDs, user, IP, and timestamps
- **Fine-grained Control**: Enable/disable individual variables, add remarks, control synchronization
- **Cache Layer**: High-performance caching with automatic invalidation on changes
- **Admin Interface**: Ready-to-use EasyAdmin CRUD controller for management
- **JSON-RPC Support**: Built-in procedure for fetching public configuration
- **Twig Integration**: Access environment variables directly in templates

## Requirements

- PHP >= 8.1
- Symfony >= 6.4
- Doctrine ORM >= 2.20

## Installation

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

### 1. Create the Database Table

```bash
# Generate migration
php bin/console make:migration

# Run migration to create base_env table
php bin/console doctrine:migrations:migrate
```

## 2. Add Environment Variables

```php
use Tourze\EnvManageBundle\Entity\Env;
use Doctrine\ORM\EntityManagerInterface;

$env = new Env();
$env->setName('API_ENDPOINT');
$env->setValue('https://api.example.com');
$env->setValid(true);
$env->setSync(false);
$env->setRemark('External API endpoint');

$entityManager->persist($env);
$entityManager->flush();
```

## 3. Access Variables

```php
// In services
$apiEndpoint = $_ENV['API_ENDPOINT'] ?? 'default';

// Using the service
/** @var \Tourze\EnvManageBundle\Service\EnvService $envService */
$publicVars = $envService->fetchPublicArray();

// In Twig templates
{{ env_value('API_ENDPOINT') }}
```

## Configuration

## Security Configuration

The bundle automatically blocks dangerous environment variables:
- `LD_PRELOAD` - Prevents injection attacks
- `APP_*` - Protects Symfony core configuration
- `DATABASE_*` - Prevents database credential override
- `REDIS_*` - Protects cache configuration
- `JWT_*` - Secures authentication tokens
- `MESSENGER_*` - Protects message queue configuration
- `LOCK_*` - Prevents lock mechanism tampering

## Admin Interface

Add to your EasyAdmin dashboard:

```yaml
# config/packages/easy_admin.yaml
easy_admin:
    entities:
        Env:
            class: Tourze\EnvManageBundle\Entity\Env
            controller: Tourze\EnvManageBundle\Controller\Admin\EnvCrudController
```

## Caching

Environment variables are cached for 24 hours and automatically invalidated when:
- Any environment variable is created, updated, or deleted
- Cache is manually cleared
- Application is deployed

## Events and Extension Points

### Event Listeners

The bundle listens to:
- `KernelEvents::REQUEST` - Load variables for HTTP requests
- `WorkerStartedEvent` - Load variables for message workers
- `ConsoleEvents::COMMAND` - Load variables for CLI commands (except cache commands)

### Entity Events

Doctrine entity listeners automatically clear cache on:
- `postPersist` - After creating new variables
- `postUpdate` - After updating variables
- `postRemove` - After deleting variables

## Advanced Usage

### Custom Environment Service

```php
use Tourze\EnvManageBundle\Service\EnvService;

class MyEnvService implements EnvService
{
    public function fetchPublicArray(): array
    {
        // Custom logic for public variables
    }
}
```

### JSON-RPC Integration

```php
// Expose environment variables via JSON-RPC
$procedure = new GetEnvConfig($envService);
$result = $procedure->execute();
```

## Documentation

- [Entity Design](ENTITY_DESIGN.md) - Database schema and entity details
- [Workflow](WORKFLOW.md) - Visual workflow diagrams
- [API Reference](docs/api.md) - Complete API documentation

## Testing

```bash
# Run tests
./vendor/bin/phpunit packages/env-manage-bundle/tests

# Run static analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/env-manage-bundle
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Run PHPStan (level 5) before submitting
- Update documentation as needed
- Add meaningful commit messages

## Security

If you discover any security-related issues, please email security@tourze.com instead of using the issue tracker.

## Credits

- [Tourze Team](https://github.com/tourze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Copyright (c) tourze. Please see [License File](LICENSE) for more information.

## Changelog

See [Releases](https://packagist.org/packages/tourze/env-manage-bundle#releases) for version history and upgrade notes.
