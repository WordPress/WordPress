<?php

namespace Matomo\Dependencies;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Matomo\Dependencies\Twig\Environment;
use Matomo\Dependencies\Twig\Extension\DebugExtension;
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
