<?php

namespace Tourze\EnvManageBundle\Service;

use Tourze\EnvManageBundle\Entity\Env;

interface EnvService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function fetchPublicArray(): array;

    /**
     * 根据名称查找环境变量
     */
    public function findByName(string $name): ?Env;

    /**
     * 根据名称和有效性查找环境变量
     */
    public function findByNameAndValid(string $name, bool $valid = true): ?Env;

    /**
     * 保存环境变量
     */
    public function saveEnv(Env $env): void;
}
