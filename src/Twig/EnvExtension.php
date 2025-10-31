<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tourze\EnvManageBundle\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Twig\Attribute\AsTwigFunction;

/**
 * See https://symfony.com/doc/current/templating/twig_extension.html.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Julien ITARD <julienitard@gmail.com>
 */
#[Autoconfigure(lazy: true)]
class EnvExtension
{
    /**
     * 加载环境变量
     */
    #[AsTwigFunction(name: 'env')]
    #[AsTwigFunction(name: 'setting')]
    public function getEnv(string $name, mixed $defaultValue = null): mixed
    {
        return $_ENV[$name] ?? $defaultValue;
    }
}
