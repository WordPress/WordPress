<?php

declare (strict_types=1);
namespace Matomo\Dependencies\DI;

use Matomo\Dependencies\DI\Compiler\Compiler;
use Matomo\Dependencies\DI\Definition\Source\AnnotationBasedAutowiring;
use Matomo\Dependencies\DI\Definition\Source\DefinitionArray;
use Matomo\Dependencies\DI\Definition\Source\DefinitionFile;
use Matomo\Dependencies\DI\Definition\Source\DefinitionSource;
use Matomo\Dependencies\DI\Definition\Source\NoAutowiring;
use Matomo\Dependencies\DI\Definition\Source\ReflectionBasedAutowiring;
use Matomo\Dependencies\DI\Definition\Source\SourceCache;
use Matomo\Dependencies\DI\Definition\Source\SourceChain;
use Matomo\Dependencies\DI\Proxy\ProxyFactory;
use InvalidArgumentException;
use Matomo\Dependencies\Psr\Container\ContainerInterface;
/**
 * Helper to create and configure a Container.
 *
 * With the default options, the container created is appropriate for the development environment.
 *
 * Example:
 *
 *     $builder = new ContainerBuilder();
 *     $container = $builder->build();
 *
 * @api
 *
 * @since  3.2
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ContainerBuilder
{
    /**
     * Name of the container class, used to create the container.
     * @var string
     */
    private $containerClass;
    /**
     * Name of the container parent class, used on compiled container.
     * @var string
     */
    private $containerParentClass;
    /**
     * @var bool
     */
    private $useAutowiring = \true;
    /**
     * @var bool
     */
    private $useAnnotations = \false;
    /**
     * @var bool
     */
    private $ignorePhpDocErrors = \false;
    /**
     * If true, write the proxies to disk to improve performances.
     * @var bool
     */
    private $writeProxiesToFile = \false;
    /**
     * Directory where to write the proxies (if $writeProxiesToFile is enabled).
     * @var string|null
     */
    private $proxyDirectory;
    /**
     * If PHP-DI is wrapped in another container, this references the wrapper.
     * @var ContainerInterface
     */
    private $wrapperContainer;
    /**
     * @var DefinitionSource[]|string[]|array[]
     */
    private $definitionSources = [];
    /**
     * Whether the container has already been built.
     * @var bool
     */
    private $locked = \false;
    /**
     * @var string|null
     */
    private $compileToDirectory;
    /**
     * @var bool
     */
    private $sourceCache = \false;
    /**
     * @var string
     */
    protected $sourceCacheNamespace;
    /**
     * Build a container configured for the dev environment.
     */
    public static function buildDevContainer() : Container
    {
        return new Container();
    }
    /**
     * @param string $containerClass Name of the container class, used to create the container.
     */
    public function __construct(string $containerClass = Container::class)
    {
        $this->containerClass = $containerClass;
    }
    /**
     * Build and return a container.
     *
     * @return Container
     */
    public function build()
    {
        $sources = array_reverse($this->definitionSources);
        if ($this->useAnnotations) {
            $autowiring = new AnnotationBasedAutowiring($this->ignorePhpDocErrors);
            $sources[] = $autowiring;
        } elseif ($this->useAutowiring) {
            $autowiring = new ReflectionBasedAutowiring();
            $sources[] = $autowiring;
        } else {
            $autowiring = new NoAutowiring();
        }
        $sources = array_map(function ($definitions) use($autowiring) {
            if (is_string($definitions)) {
                // File
                return new DefinitionFile($definitions, $autowiring);
            } elseif (is_array($definitions)) {
                return new DefinitionArray($definitions, $autowiring);
            }
            return $definitions;
        }, $sources);
        $source = new SourceChain($sources);
        // Mutable definition source
        $source->setMutableDefinitionSource(new DefinitionArray([], $autowiring));
        if ($this->sourceCache) {
            if (!SourceCache::isSupported()) {
                throw new \Exception('APCu is not enabled, PHP-DI cannot use it as a cache');
            }
            // Wrap the source with the cache decorator
            $source = new SourceCache($source, $this->sourceCacheNamespace);
        }
        $proxyFactory = new ProxyFactory($this->writeProxiesToFile, $this->proxyDirectory);
        $this->locked = \true;
        $containerClass = $this->containerClass;
        if ($this->compileToDirectory) {
            $compiler = new Compiler($proxyFactory);
            $compiledContainerFile = $compiler->compile($source, $this->compileToDirectory, $containerClass, $this->containerParentClass, $this->useAutowiring || $this->useAnnotations);
            // Only load the file if it hasn't been already loaded
            // (the container can be created multiple times in the same process)
            if (!class_exists($containerClass, \false)) {
                require $compiledContainerFile;
            }
        }
        return new $containerClass($source, $proxyFactory, $this->wrapperContainer);
    }
    /**
     * Compile the container for optimum performances.
     *
     * Be aware that the container is compiled once and never updated!
     *
     * Therefore:
     *
     * - in production you should clear that directory every time you deploy
     * - in development you should not compile the container
     *
     * @see https://php-di.org/doc/performances.html
     *
     * @param string $directory Directory in which to put the compiled container.
     * @param string $containerClass Name of the compiled class. Customize only if necessary.
     * @param string $containerParentClass Name of the compiled container parent class. Customize only if necessary.
     */
    public function enableCompilation(string $directory, string $containerClass = 'CompiledContainer', string $containerParentClass = CompiledContainer::class) : self
    {
        $this->ensureNotLocked();
        $this->compileToDirectory = $directory;
        $this->containerClass = $containerClass;
        $this->containerParentClass = $containerParentClass;
        return $this;
    }
    /**
     * Enable or disable the use of autowiring to guess injections.
     *
     * Enabled by default.
     *
     * @return $this
     */
    public function useAutowiring(bool $bool) : self
    {
        $this->ensureNotLocked();
        $this->useAutowiring = $bool;
        return $this;
    }
    /**
     * Enable or disable the use of annotations to guess injections.
     *
     * Disabled by default.
     *
     * @return $this
     */
    public function useAnnotations(bool $bool) : self
    {
        $this->ensureNotLocked();
        $this->useAnnotations = $bool;
        return $this;
    }
    /**
     * Enable or disable ignoring phpdoc errors (non-existent classes in `@param` or `@var`).
     *
     * @return $this
     */
    public function ignorePhpDocErrors(bool $bool) : self
    {
        $this->ensureNotLocked();
        $this->ignorePhpDocErrors = $bool;
        return $this;
    }
    /**
     * Configure the proxy generation.
     *
     * For dev environment, use `writeProxiesToFile(false)` (default configuration)
     * For production environment, use `writeProxiesToFile(true, 'tmp/proxies')`
     *
     * @see https://php-di.org/doc/lazy-injection.html
     *
     * @param bool $writeToFile If true, write the proxies to disk to improve performances
     * @param string|null $proxyDirectory Directory where to write the proxies
     * @throws InvalidArgumentException when writeToFile is set to true and the proxy directory is null
     * @return $this
     */
    public function writeProxiesToFile(bool $writeToFile, string $proxyDirectory = null) : self
    {
        $this->ensureNotLocked();
        $this->writeProxiesToFile = $writeToFile;
        if ($writeToFile && $proxyDirectory === null) {
            throw new InvalidArgumentException('The proxy directory must be specified if you want to write proxies on disk');
        }
        $this->proxyDirectory = $proxyDirectory;
        return $this;
    }
    /**
     * If PHP-DI's container is wrapped by another container, we can
     * set this so that PHP-DI will use the wrapper rather than itself for building objects.
     *
     * @return $this
     */
    public function wrapContainer(ContainerInterface $otherContainer) : self
    {
        $this->ensureNotLocked();
        $this->wrapperContainer = $otherContainer;
        return $this;
    }
    /**
     * Add definitions to the container.
     *
     * @param string|array|DefinitionSource ...$definitions Can be an array of definitions, the
     *                                                      name of a file containing definitions
     *                                                      or a DefinitionSource object.
     * @return $this
     */
    public function addDefinitions(...$definitions) : self
    {
        $this->ensureNotLocked();
        foreach ($definitions as $definition) {
            if (!is_string($definition) && !is_array($definition) && !$definition instanceof DefinitionSource) {
                throw new InvalidArgumentException(sprintf('%s parameter must be a string, an array or a DefinitionSource object, %s given', 'ContainerBuilder::addDefinitions()', is_object($definition) ? get_class($definition) : gettype($definition)));
            }
            $this->definitionSources[] = $definition;
        }
        return $this;
    }
    /**
     * Enables the use of APCu to cache definitions.
     *
     * You must have APCu enabled to use it.
     *
     * Before using this feature, you should try these steps first:
     * - enable compilation if not already done (see `enableCompilation()`)
     * - if you use autowiring or annotations, add all the classes you are using into your configuration so that
     *   PHP-DI knows about them and compiles them
     * Once this is done, you can try to optimize performances further with APCu. It can also be useful if you use
     * `Container::make()` instead of `get()` (`make()` calls cannot be compiled so they are not optimized).
     *
     * Remember to clear APCu on each deploy else your application will have a stale cache. Do not enable the cache
     * in development environment: any change you will make to the code will be ignored because of the cache.
     *
     * @see https://php-di.org/doc/performances.html
     *
     * @param string $cacheNamespace use unique namespace per container when sharing a single APC memory pool to prevent cache collisions
     * @return $this
     */
    public function enableDefinitionCache(string $cacheNamespace = '') : self
    {
        $this->ensureNotLocked();
        $this->sourceCache = \true;
        $this->sourceCacheNamespace = $cacheNamespace;
        return $this;
    }
    /**
     * Are we building a compiled container?
     */
    public function isCompilationEnabled() : bool
    {
        return (bool) $this->compileToDirectory;
    }
    private function ensureNotLocked()
    {
        if ($this->locked) {
            throw new \LogicException('The ContainerBuilder cannot be modified after the container has been built');
        }
    }
}
