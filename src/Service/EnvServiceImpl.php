<?php

namespace Tourze\EnvManageBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Tourze\EnvManageBundle\Repository\EnvRepository;

#[AsAlias(EnvService::class)]
class EnvServiceImpl implements EnvService
{
    public function __construct(private readonly EnvRepository $envRepository)
    {
    }

    public function fetchPublicArray(): array
    {
        $data = $this->envRepository->findBy([
            'valid' => true,
            'sync' => true,
        ]);

        $result = [];
        foreach ($data as $datum) {
            $result[] = $datum->retrieveApiArray();
        }

        return $result;
    }
}
