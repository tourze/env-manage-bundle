<?php

namespace Tourze\EnvManageBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Repository\EnvRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(EnvRepository::class)]
#[RunTestsInSeparateProcesses]
final class EnvRepositoryTest extends AbstractRepositoryTestCase
{
    private EnvRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(EnvRepository::class);
    }

    public function testFindBy(): void
    {
        $entity = $this->repository->findOneBy(['name' => 'APP_ENV']);
        $this->assertNotNull($entity);
        $this->assertEquals('APP_ENV', $entity->getName());
    }

    public function testPersist(): void
    {
        $name = 'TEST_ENV' . uniqid();

        $entity = $this->repository->findOneBy(['name' => $name]);
        $this->assertNull($entity);

        $entity = new Env();
        $entity->setName($name);
        $entity->setValue('test_value');
        $entity->setValid(true);
        $entity->setSync(true);
        $this->repository->save($entity);

        $entity = $this->repository->findOneBy(['name' => $name]);
        $this->assertNotNull($entity);
    }

    public function testSave(): void
    {
        $name = 'SAVE_TEST_' . uniqid();

        $entity = new Env();
        $entity->setName($name);
        $entity->setValue('save_test_value');
        $entity->setValid(true);
        $entity->setSync(true);

        $this->repository->save($entity);

        $foundEntity = $this->repository->findOneBy(['name' => $name]);
        $this->assertNotNull($foundEntity);
        $this->assertEquals($name, $foundEntity->getName());
        $this->assertEquals('save_test_value', $foundEntity->getValue());
    }

    public function testSaveWithoutFlush(): void
    {
        $name = 'SAVE_NO_FLUSH_' . uniqid();

        $entity = new Env();
        $entity->setName($name);
        $entity->setValue('no_flush_value');
        $entity->setValid(true);
        $entity->setSync(true);

        $this->repository->save($entity, false);

        self::getEntityManager()->flush();

        $foundEntity = $this->repository->findOneBy(['name' => $name]);
        $this->assertNotNull($foundEntity);
        $this->assertEquals($name, $foundEntity->getName());
    }

    public function testRemove(): void
    {
        $name = 'REMOVE_TEST_' . uniqid();

        $entity = new Env();
        $entity->setName($name);
        $entity->setValue('remove_test_value');
        $entity->setValid(true);
        $entity->setSync(true);

        $this->repository->save($entity);

        $foundEntity = $this->repository->findOneBy(['name' => $name]);
        $this->assertNotNull($foundEntity);

        $this->repository->remove($foundEntity);

        $removedEntity = $this->repository->findOneBy(['name' => $name]);
        $this->assertNull($removedEntity);
    }

    public function testFindByWithNullCriteria(): void
    {
        $entities = $this->repository->findBy(['valid' => null]);
        $this->assertIsArray($entities);
        foreach ($entities as $entity) {
            $this->assertNull($entity->isValid());
        }
    }

    public function testFindOneByWithNullCriteria(): void
    {
        $entity = $this->repository->findOneBy(['remark' => null]);
        if (null !== $entity) {
            $this->assertNull($entity->getRemark());
        }

        // 验证查询不会抛出异常,无论是否找到结果
        $this->assertTrue(true, 'findOneBy with null criteria executed without throwing exception');
    }

    public function testCountWithNullCriteria(): void
    {
        $count = $this->repository->count(['remark' => null]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindOneByWithOrderBy(): void
    {
        $name1 = 'ONEBY_ORDER_A_' . uniqid();

        $entity1 = new Env();
        $entity1->setName($name1);
        $entity1->setValue('value1');
        $entity1->setValid(true);
        $entity1->setSync(true);

        $this->repository->save($entity1);

        $entity = $this->repository->findOneBy(
            ['valid' => true],
            ['name' => 'DESC']
        );

        $this->assertNotNull($entity);
        $this->assertTrue($entity->isValid());
    }

    public function testFindByWithSyncNullCriteria(): void
    {
        $entities = $this->repository->findBy(['sync' => null]);
        $this->assertIsArray($entities);
        foreach ($entities as $entity) {
            $this->assertNull($entity->isSync());
        }
    }

    public function testCountWithSyncNullCriteria(): void
    {
        $count = $this->repository->count(['sync' => null]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithValidNullCriteria(): void
    {
        $count = $this->repository->count(['valid' => null]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindOneByWithOrderByLogic(): void
    {
        $name1 = 'ONEBY_ORDER_LOGIC_A_' . uniqid();
        $name2 = 'ONEBY_ORDER_LOGIC_B_' . uniqid();

        $entity1 = new Env();
        $entity1->setName($name1);
        $entity1->setValue('value1');
        $entity1->setValid(true);
        $entity1->setSync(true);

        $entity2 = new Env();
        $entity2->setName($name2);
        $entity2->setValue('value2');
        $entity2->setValid(true);
        $entity2->setSync(true);

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        $firstEntity = $this->repository->findOneBy(
            ['valid' => true],
            ['name' => 'ASC']
        );

        $lastEntity = $this->repository->findOneBy(
            ['valid' => true],
            ['name' => 'DESC']
        );

        $this->assertNotNull($firstEntity);
        $this->assertNotNull($lastEntity);
        $this->assertNotEquals($firstEntity->getId(), $lastEntity->getId());
    }

    public function testFindOneByWithOrderByAndLimitLogic(): void
    {
        $baseName = 'FINDONE_ORDER_' . uniqid();
        $name1 = $baseName . '_A';
        $name2 = $baseName . '_B';
        $name3 = $baseName . '_C';

        $entity1 = new Env();
        $entity1->setName($name1);
        $entity1->setValue('value1');
        $entity1->setValid(true);
        $entity1->setSync(true);

        $entity2 = new Env();
        $entity2->setName($name2);
        $entity2->setValue('value2');
        $entity2->setValid(true);
        $entity2->setSync(true);

        $entity3 = new Env();
        $entity3->setName($name3);
        $entity3->setValue('value3');
        $entity3->setValid(true);
        $entity3->setSync(true);

        $this->repository->save($entity1);
        $this->repository->save($entity2);
        $this->repository->save($entity3);

        $firstEntity = $this->repository->findOneBy(
            ['valid' => true, 'sync' => true],
            ['name' => 'ASC']
        );

        $lastEntity = $this->repository->findOneBy(
            ['valid' => true, 'sync' => true],
            ['name' => 'DESC']
        );

        $this->assertNotNull($firstEntity);
        $this->assertNotNull($lastEntity);
        $this->assertTrue($firstEntity->isValid());
        $this->assertTrue($lastEntity->isValid());
        $this->assertNotEquals($firstEntity->getId(), $lastEntity->getId());

        $allMatchingEntities = $this->repository->findBy(
            ['valid' => true, 'sync' => true],
            ['name' => 'ASC']
        );
        $this->assertGreaterThanOrEqual(3, count($allMatchingEntities));

        foreach ($allMatchingEntities as $i => $entity) {
            if ($i > 0) {
                $this->assertGreaterThanOrEqual(
                    $allMatchingEntities[$i - 1]->getName(),
                    $entity->getName(),
                    'Entities should be ordered by name ASC'
                );
            }
        }
    }

    public function testFindByWithRemarkIsNull(): void
    {
        $nameWithNull = 'REMARK_NULL_TEST_' . uniqid();
        $nameWithValue = 'REMARK_VALUE_TEST_' . uniqid();

        $entityWithNullRemark = new Env();
        $entityWithNullRemark->setName($nameWithNull);
        $entityWithNullRemark->setValue('test_value');
        $entityWithNullRemark->setRemark(null);
        $entityWithNullRemark->setValid(true);
        $entityWithNullRemark->setSync(true);

        $entityWithValueRemark = new Env();
        $entityWithValueRemark->setName($nameWithValue);
        $entityWithValueRemark->setValue('test_value2');
        $entityWithValueRemark->setRemark('has remark');
        $entityWithValueRemark->setValid(true);
        $entityWithValueRemark->setSync(true);

        $this->repository->save($entityWithNullRemark);
        $this->repository->save($entityWithValueRemark);

        $nullRemarkEntities = $this->repository->findBy(['remark' => null]);
        $this->assertIsArray($nullRemarkEntities);

        $foundNullRemarkEntity = false;
        foreach ($nullRemarkEntities as $entity) {
            $this->assertNull($entity->getRemark());
            if ($entity->getName() === $nameWithNull) {
                $foundNullRemarkEntity = true;
            }
        }
        $this->assertTrue($foundNullRemarkEntity, 'Should find entity with null remark');
    }

    public function testFindByWithSyncIsNull(): void
    {
        $nameWithNull = 'SYNC_NULL_TEST_' . uniqid();

        $entityWithNullSync = new Env();
        $entityWithNullSync->setName($nameWithNull);
        $entityWithNullSync->setValue('test_value');
        $entityWithNullSync->setSync(null);
        $entityWithNullSync->setValid(true);

        $this->repository->save($entityWithNullSync);

        $nullSyncEntities = $this->repository->findBy(['sync' => null]);
        $this->assertIsArray($nullSyncEntities);

        $foundNullSyncEntity = false;
        foreach ($nullSyncEntities as $entity) {
            $this->assertNull($entity->isSync());
            if ($entity->getName() === $nameWithNull) {
                $foundNullSyncEntity = true;
            }
        }
        $this->assertTrue($foundNullSyncEntity, 'Should find entity with null sync');
    }

    public function testFindByWithValidIsNull(): void
    {
        $nameWithNull = 'VALID_NULL_TEST_' . uniqid();

        $entityWithNullValid = new Env();
        $entityWithNullValid->setName($nameWithNull);
        $entityWithNullValid->setValue('test_value');
        $entityWithNullValid->setValid(null);
        $entityWithNullValid->setSync(true);

        $this->repository->save($entityWithNullValid);

        $nullValidEntities = $this->repository->findBy(['valid' => null]);
        $this->assertIsArray($nullValidEntities);

        $foundNullValidEntity = false;
        foreach ($nullValidEntities as $entity) {
            $this->assertNull($entity->isValid());
            if ($entity->getName() === $nameWithNull) {
                $foundNullValidEntity = true;
            }
        }
        $this->assertTrue($foundNullValidEntity, 'Should find entity with null valid');
    }

    public function testCountWithRemarkIsNull(): void
    {
        $nameWithNull = 'COUNT_REMARK_NULL_' . uniqid();

        $entityWithNullRemark = new Env();
        $entityWithNullRemark->setName($nameWithNull);
        $entityWithNullRemark->setValue('test_value');
        $entityWithNullRemark->setRemark(null);
        $entityWithNullRemark->setValid(true);
        $entityWithNullRemark->setSync(true);

        $this->repository->save($entityWithNullRemark);

        $countBefore = $this->repository->count(['remark' => null]);
        $this->assertIsInt($countBefore);
        $this->assertGreaterThanOrEqual(1, $countBefore);

        $entityWithRemark = new Env();
        $entityWithRemark->setName($nameWithNull . '_with_remark');
        $entityWithRemark->setValue('test_value2');
        $entityWithRemark->setRemark('has remark');
        $entityWithRemark->setValid(true);
        $entityWithRemark->setSync(true);

        $this->repository->save($entityWithRemark);

        $countAfter = $this->repository->count(['remark' => null]);
        $this->assertEquals($countBefore, $countAfter, 'Count should remain same after adding entity with remark');
    }

    public function testCountWithSyncIsNull(): void
    {
        $countBefore = $this->repository->count(['sync' => null]);

        $nameWithNull = 'COUNT_SYNC_NULL_' . uniqid();
        $entityWithNullSync = new Env();
        $entityWithNullSync->setName($nameWithNull);
        $entityWithNullSync->setValue('test_value');
        $entityWithNullSync->setSync(null);
        $entityWithNullSync->setValid(true);

        $this->repository->save($entityWithNullSync);

        $countAfter = $this->repository->count(['sync' => null]);
        $this->assertIsInt($countAfter);
        $this->assertEquals($countBefore + 1, $countAfter, 'Count should increase by 1 after adding null sync entity');
    }

    public function testCountWithValidIsNull(): void
    {
        $countBefore = $this->repository->count(['valid' => null]);

        $nameWithNull = 'COUNT_VALID_NULL_' . uniqid();
        $entityWithNullValid = new Env();
        $entityWithNullValid->setName($nameWithNull);
        $entityWithNullValid->setValue('test_value');
        $entityWithNullValid->setValid(null);
        $entityWithNullValid->setSync(true);

        $this->repository->save($entityWithNullValid);

        $countAfter = $this->repository->count(['valid' => null]);
        $this->assertIsInt($countAfter);
        $this->assertEquals($countBefore + 1, $countAfter, 'Count should increase by 1 after adding null valid entity');
    }

    public function testFindOneByWithSortingLogicForNullableFields(): void
    {
        $name1 = 'SORT_TEST_A_' . uniqid();
        $name2 = 'SORT_TEST_B_' . uniqid();

        $entity1 = new Env();
        $entity1->setName($name1);
        $entity1->setValue('value1');
        $entity1->setValid(null);
        $entity1->setRemark('a remark');
        $entity1->setSync(true);

        $entity2 = new Env();
        $entity2->setName($name2);
        $entity2->setValue('value2');
        $entity2->setValid(true);
        $entity2->setRemark(null);
        $entity2->setSync(null);

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        $entityWithNullRemark = $this->repository->findOneBy(
            ['remark' => null],
            ['name' => 'ASC']
        );
        $this->assertNotNull($entityWithNullRemark);
        $this->assertNull($entityWithNullRemark->getRemark());

        $entityWithNullSync = $this->repository->findOneBy(
            ['sync' => null],
            ['name' => 'DESC']
        );
        $this->assertNotNull($entityWithNullSync);
        $this->assertNull($entityWithNullSync->isSync());

        $entityWithNullValid = $this->repository->findOneBy(
            ['valid' => null],
            ['name' => 'ASC']
        );
        $this->assertNotNull($entityWithNullValid);
        $this->assertNull($entityWithNullValid->isValid());
    }

    /**
     * @return ServiceEntityRepository<Env>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $entity = new Env();
        $entity->setName('TEST_ENV_' . uniqid());
        $entity->setValue('test_value');
        $entity->setValid(true);
        $entity->setSync(true);

        return $entity;
    }
}
