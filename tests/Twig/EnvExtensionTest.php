<?php

namespace Tourze\EnvManageBundle\Tests\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EnvManageBundle\Twig\EnvExtension;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(EnvExtension::class)]
#[RunTestsInSeparateProcesses]
final class EnvExtensionTest extends AbstractIntegrationTestCase
{
    private EnvExtension $extension;

    protected function onSetUp(): void
    {
        /** @var EnvExtension $extension */
        $extension = self::getContainer()->get(EnvExtension::class);
        $this->extension = $extension;
    }

    public function testExtensionInstanceCreation(): void
    {
        $this->assertInstanceOf(EnvExtension::class, $this->extension);
    }

    public function testGetEnvWithExistingVariableReturnsValue(): void
    {
        $_ENV['TEST_VAR'] = 'test_value';

        $result = $this->extension->getEnv('TEST_VAR');

        $this->assertSame('test_value', $result);

        unset($_ENV['TEST_VAR']);
    }

    public function testGetEnvWithNonExistingVariableReturnsDefaultValue(): void
    {
        $result = $this->extension->getEnv('NON_EXISTING_VAR', 'default_value');

        $this->assertSame('default_value', $result);
    }

    public function testGetEnvWithNonExistingVariableAndNoDefaultReturnsNull(): void
    {
        $result = $this->extension->getEnv('NON_EXISTING_VAR');

        $this->assertNull($result);
    }

    public function testGetEnvWithVariousDataTypesReturnsCorrectTypes(): void
    {
        $_ENV['TEST_STRING'] = 'string_value';
        $_ENV['TEST_INT'] = '123';
        $_ENV['TEST_BOOL'] = '1';

        $this->assertSame('string_value', $this->extension->getEnv('TEST_STRING'));
        $this->assertSame('123', $this->extension->getEnv('TEST_INT'));
        $this->assertSame('1', $this->extension->getEnv('TEST_BOOL'));

        unset($_ENV['TEST_STRING'], $_ENV['TEST_INT'], $_ENV['TEST_BOOL']);
    }
}
