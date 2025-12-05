<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI\Proxy;

use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use ProxyManager\Proxy\LazyLoadingInterface;
/**
 * Creates proxy classes.
 *
 * Wraps Ocramius/ProxyManager LazyLoadingValueHolderFactory.
 *
 * @see \ProxyManager\Factory\LazyLoadingValueHolderFactory
 *
 * @since  5.0
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ProxyFactory
{
    /**
     * If true, write the proxies to disk to improve performances.
     * @var bool
     */
    private $writeProxiesToFile;
    /**
     * Directory where to write the proxies (if $writeProxiesToFile is enabled).
     * @var string|null
     */
    private $proxyDirectory;
    /**
     * @var LazyLoadingValueHolderFactory|null
     */
    private $proxyManager;
    public function __construct(bool $writeProxiesToFile = \false, string $proxyDirectory = null)
    {
        $this->writeProxiesToFile = $writeProxiesToFile;
        $this->proxyDirectory = $proxyDirectory;
    }
    /**
     * Creates a new lazy proxy instance of the given class with
     * the given initializer.
     *
     * @param string $className name of the class to be proxied
     * @param \Closure $initializer initializer to be passed to the proxy
     */
    public function createProxy(string $className, \Closure $initializer) : LazyLoadingInterface
    {
        $this->createProxyManager();
        return $this->proxyManager->createProxy($className, $initializer);
    }
    /**
     * Generates and writes the proxy class to file.
     *
     * @param string $className name of the class to be proxied
     */
    public function generateProxyClass(string $className)
    {
        // If proxy classes a written to file then we pre-generate the class
        // If they are not written to file then there is no point to do this
        if ($this->writeProxiesToFile) {
            $this->createProxyManager();
            $this->createProxy($className, function () {
            });
        }
    }
    private function createProxyManager()
    {
        if ($this->proxyManager !== null) {
            return;
        }
        if (!class_exists(Configuration::class)) {
            throw new \RuntimeException('The ocramius/proxy-manager library is not installed. Lazy injection requires that library to be installed with Composer in order to work. Run "composer require ocramius/proxy-manager:~2.0".');
        }
        $config = new Configuration();
        if ($this->writeProxiesToFile) {
            $config->setProxiesTargetDir($this->proxyDirectory);
            $config->setGeneratorStrategy(new FileWriterGeneratorStrategy(new FileLocator($this->proxyDirectory)));
            // @phpstan-ignore-next-line
            spl_autoload_register($config->getProxyAutoloader());
        } else {
            $config->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        }
        $this->proxyManager = new LazyLoadingValueHolderFactory($config);
    }
}
