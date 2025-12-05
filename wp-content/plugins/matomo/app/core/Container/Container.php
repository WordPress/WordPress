<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Container;

use Matomo\Dependencies\DI\Container as DIContainer;
use Matomo\Dependencies\DI\Definition\Source\MutableDefinitionSource;
use Matomo\Dependencies\DI\Proxy\ProxyFactory;
use Piwik\Exception\DI\DependencyException;
use Piwik\Exception\DI\NotFoundException;
use Matomo\Dependencies\Psr\Container\ContainerInterface;
/**
 * Proxy class for our DI Container
 * @see DIContainer, ContainerInterface
 */
class Container extends DIContainer implements ContainerInterface
{
    public function __construct(?MutableDefinitionSource $definitionSource = null, ?ProxyFactory $proxyFactory = null, ?ContainerInterface $wrapperContainer = null)
    {
        parent::__construct($definitionSource, $proxyFactory, $wrapperContainer);
        // ensure this container class can be resolved
        $this->resolvedEntries[self::class] = $this;
    }
    public function get($name)
    {
        try {
            return parent::get($name);
        } catch (\Matomo\Dependencies\DI\NotFoundException $e) {
            throw new NotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function make($name, array $parameters = [])
    {
        try {
            return parent::make($name, $parameters);
        } catch (\Matomo\Dependencies\DI\NotFoundException $e) {
            throw new NotFoundException($e->getMessage(), $e->getCode(), $e);
        } catch (\Matomo\Dependencies\DI\DependencyException $e) {
            throw new DependencyException($e->getMessage(), $e->getCode(), $e);
        }
    }
    public function injectOn($instance)
    {
        try {
            return parent::injectOn($instance);
        } catch (\Matomo\Dependencies\DI\DependencyException $e) {
            throw new DependencyException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
