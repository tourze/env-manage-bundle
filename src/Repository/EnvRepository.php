<?php

namespace Tourze\EnvManageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EnvManageBundle\Entity\Env;

/**
 * @method Env|null find($id, $lockMode = null, $lockVersion = null)
 * @method Env|null findOneBy(array $criteria, array $orderBy = null)
 * @method Env[]    findAll()
 * @method Env[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
#[Autoconfigure(public: true)]
class EnvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Env::class);
    }
}
