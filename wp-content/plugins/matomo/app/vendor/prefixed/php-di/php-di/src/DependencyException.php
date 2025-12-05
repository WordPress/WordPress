<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI;

use Matomo\Dependencies\Psr\Container\ContainerExceptionInterface;
/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
