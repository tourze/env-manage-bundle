<?php

namespace Tourze\EnvManageBundle\Tests\Controller\Admin;

use Symfony\Component\Security\Core\User\UserInterface;

class TestUser implements UserInterface
{
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return 'test-admin';
    }
}
