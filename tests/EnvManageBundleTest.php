<?php

declare(strict_types=1);

namespace Tourze\EnvManageBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EnvManageBundle\EnvManageBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(EnvManageBundle::class)]
#[RunTestsInSeparateProcesses]
final class EnvManageBundleTest extends AbstractBundleTestCase
{
}
