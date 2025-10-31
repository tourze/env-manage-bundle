<?php

namespace Tourze\EnvManageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Repository\EnvRepository;
use Tourze\EnvManageBundle\Service\EnvServiceImpl;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(EnvServiceImpl::class)]
#[RunTestsInSeparateProcesses]
final class EnvServiceTest extends AbstractIntegrationTestCase
{
    private EnvServiceImpl $envService;

    protected function onSetUp(): void
    {
        /** @var EnvServiceImpl $envService */
        $envService = self::getContainer()->get(EnvServiceImpl::class);
        $this->envService = $envService;
    }

    public function testFetchPublicArrayWithNoEnvironmentVariablesReturnsEmptyArray(): void
    {
        $result = $this->envService->fetchPublicArray();

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertIsArray($result);
    }

    public function testFetchPublicArrayWithEnvironmentVariablesReturnsFormattedArray(): void
    {
        $env1 = new Env();
        $env1->setName('TEST_ENV1');
        $env1->setValue('value1');
        $env1->setValid(true);
        $env1->setSync(true);

        $env2 = new Env();
        $env2->setName('TEST_ENV2');
        $env2->setValue('value2');
        $env2->setValid(true);
        $env2->setSync(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($env1);
        $entityManager->persist($env2);
        $entityManager->flush();

        $result = $this->envService->fetchPublicArray();

        $this->assertGreaterThanOrEqual(2, count($result));

        $foundEnv1 = false;
        $foundEnv2 = false;

        foreach ($result as $env) {
            $this->assertArrayHasKey('name', $env);
            $this->assertArrayHasKey('value', $env);

            if ('TEST_ENV1' === $env['name'] && 'value1' === $env['value']) {
                $foundEnv1 = true;
            }
            if ('TEST_ENV2' === $env['name'] && 'value2' === $env['value']) {
                $foundEnv2 = true;
            }
        }

        $this->assertTrue($foundEnv1, 'TEST_ENV1 should be found in result');
        $this->assertTrue($foundEnv2, 'TEST_ENV2 should be found in result');
    }

    public function testFindByName(): void
    {
        $env = new Env();
        $env->setName('TEST_ENV');
        $env->setValue('test_value');

        $entityManager = self::getEntityManager();
        $entityManager->persist($env);
        $entityManager->flush();

        $result = $this->envService->findByName('TEST_ENV');

        $this->assertInstanceOf(Env::class, $result);
        $this->assertEquals('TEST_ENV', $result->getName());
        $this->assertEquals('test_value', $result->getValue());
    }

    public function testFindByNameAndValid(): void
    {
        $env = new Env();
        $env->setName('TEST_ENV');
        $env->setValue('test_value');
        $env->setValid(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($env);
        $entityManager->flush();

        $result = $this->envService->findByNameAndValid('TEST_ENV', true);

        $this->assertInstanceOf(Env::class, $result);
        $this->assertEquals('TEST_ENV', $result->getName());
        $this->assertEquals('test_value', $result->getValue());
        $this->assertTrue($result->isValid());
    }

    public function testSaveEnv(): void
    {
        $env = new Env();
        $env->setName('TEST_ENV');
        $env->setValue('test_value');

        $this->envService->saveEnv($env);

        /** @var EnvRepository $repository */
        $repository = self::getService(EnvRepository::class);
        $savedEnv = $repository->findOneBy(['name' => 'TEST_ENV']);

        $this->assertInstanceOf(Env::class, $savedEnv);
        $this->assertEquals('TEST_ENV', $savedEnv->getName());
        $this->assertEquals('test_value', $savedEnv->getValue());
    }
}
