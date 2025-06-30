<?php

namespace Tourze\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Tourze\EnvManageBundle\Twig\EnvExtension;
use Twig\TwigFunction;

class EnvExtensionTest extends TestCase
{
    private EnvExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new EnvExtension();
    }

    public function testGetFunctions_returnsExpectedFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(2, $functions);

        $envFunction = $functions[0];
        $this->assertInstanceOf(TwigFunction::class, $envFunction);
        $this->assertSame('env', $envFunction->getName());

        $settingFunction = $functions[1];
        $this->assertInstanceOf(TwigFunction::class, $settingFunction);
        $this->assertSame('setting', $settingFunction->getName());
    }

    public function testGetEnv_withExistingVariable_returnsValue(): void
    {
        $_ENV['TEST_VAR'] = 'test_value';

        $result = $this->extension->getEnv('TEST_VAR');

        $this->assertSame('test_value', $result);

        unset($_ENV['TEST_VAR']);
    }

    public function testGetEnv_withNonExistingVariable_returnsDefaultValue(): void
    {
        $result = $this->extension->getEnv('NON_EXISTING_VAR', 'default_value');

        $this->assertSame('default_value', $result);
    }

    public function testGetEnv_withNonExistingVariableAndNoDefault_returnsNull(): void
    {
        $result = $this->extension->getEnv('NON_EXISTING_VAR');

        $this->assertNull($result);
    }

    public function testGetEnv_withVariousDataTypes_returnsCorrectTypes(): void
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
