<?php

namespace Tourze\Tests\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Repository\EnvRepository;

class EnvRepositoryTest extends TestCase
{
    private ManagerRegistry|MockObject $registry;
    private EntityManagerInterface|MockObject $entityManager;
    private Connection|MockObject $connection;

    private EnvRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->connection = $this->createMock(Connection::class);

        $this->registry->method('getManagerForClass')
            ->with(Env::class)
            ->willReturn($this->entityManager);

        $this->entityManager->method('getConnection')
            ->willReturn($this->connection);

        $this->repository = new EnvRepository($this->registry);
    }

    public function testRepositoryIsInstantiable(): void
    {
        $this->assertInstanceOf(EnvRepository::class, $this->repository);
    }

    public function testRepositoryExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $this->repository);
    }
}
