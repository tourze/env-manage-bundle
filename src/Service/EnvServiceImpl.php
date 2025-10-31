<?php

namespace Tourze\EnvManageBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\EnvManageBundle\Entity\Env;
use Tourze\EnvManageBundle\Repository\EnvRepository;

#[AsAlias(id: EnvService::class)]
final class EnvServiceImpl implements EnvService
{
    public function __construct(
        private readonly EnvRepository $envRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchPublicArray(): array
    {
        $data = $this->envRepository->findBy([
            'valid' => true,
            'sync' => true,
        ]);

        $result = [];
        foreach ($data as $datum) {
            $result[] = [
                'id' => $datum->getId(),
                'name' => $datum->getName(),
                'value' => $datum->getValue(),
            ];
        }

        return $result;
    }

    public function findByName(string $name): ?Env
    {
        return $this->envRepository->findOneBy(['name' => $name]);
    }

    public function findByNameAndValid(string $name, bool $valid = true): ?Env
    {
        return $this->envRepository->findOneBy(['name' => $name, 'valid' => $valid]);
    }

    public function saveEnv(Env $env): void
    {
        $this->entityManager->persist($env);
        $this->entityManager->flush();
    }
}
