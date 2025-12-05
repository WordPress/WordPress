<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ElementorDeps\Twig\Cache;

/**
 * Chains several caches together.
 *
 * Cached items are fetched from the first cache having them in its data store.
 * They are saved and deleted in all adapters at once.
 *
 * @author Quentin Devos <quentin@devos.pm>
 */
final class ChainCache implements CacheInterface
{
    private $caches;
    /**
     * @param iterable<CacheInterface> $caches The ordered list of caches used to store and fetch cached items
     */
    public function __construct(iterable $caches)
    {
        $this->caches = $caches;
    }
    public function generateKey(string $name, string $className) : string
    {
        return $className . '#' . $name;
    }
    public function write(string $key, string $content) : void
    {
        $splitKey = $this->splitKey($key);
        foreach ($this->caches as $cache) {
            $cache->write($cache->generateKey(...$splitKey), $content);
        }
    }
    public function load(string $key) : void
    {
        [$name, $className] = $this->splitKey($key);
        foreach ($this->caches as $cache) {
            $cache->load($cache->generateKey($name, $className));
            if (\class_exists($className, \false)) {
                break;
            }
        }
    }
    public function getTimestamp(string $key) : int
    {
        $splitKey = $this->splitKey($key);
        foreach ($this->caches as $cache) {
            if (0 < ($timestamp = $cache->getTimestamp($cache->generateKey(...$splitKey)))) {
                return $timestamp;
            }
        }
        return 0;
    }
    /**
     * @return string[]
     */
    private function splitKey(string $key) : array
    {
        return \array_reverse(\explode('#', $key, 2));
    }
}
