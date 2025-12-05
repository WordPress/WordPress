<?php

namespace ElementorDeps;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ElementorDeps\Twig\Environment;
use ElementorDeps\Twig\Extension\DebugExtension;
/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function twig_var_dump(Environment $env, $context, ...$vars)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
    DebugExtension::dump($env, $context, ...$vars);
}
