<?php

declare (strict_types=1);
namespace WordPress\AiClient\Common\Traits;

use WordPress\AiClient\AiClient;
/**
 * Trait for objects that cache data using PSR-16 cache with in-memory fallback.
 *
 * When a PSR-16 cache is configured via AiClient::setCache(), data is stored persistently.
 * Otherwise, data is cached in-memory for the duration of the request.
 *
 * @since 0.4.0
 */
trait WithDataCachingTrait
{
    /**
     * In-memory cache used when no PSR-16 cache is configured.
     *
     * @since 0.4.0
     *
     * @var array<string, mixed>
     */
    private array $localCache = [];
    /**
     * Gets the cache key suffixes managed by this object.
     *
     * @since 0.4.0
     *
     * @return list<string> The cache key suffixes.
     */
    abstract protected function getCachedKeys(): array;
    /**
     * Gets the base cache key for this object.
     *
     * The base cache key is used as a prefix for all cache keys managed by this object.
     * It should be unique to the implementing class to avoid cache key collisions.
     *
     * @since 0.4.0
     *
     * @return string The base cache key.
     */
    abstract protected function getBaseCacheKey(): string;
    /**
     * Checks if a value exists in the cache.
     *
     * @since 0.4.0
     *
     * @param string $key The cache key suffix (will be appended to the base key).
     * @return bool True if the value exists in cache, false otherwise.
     */
    protected function hasCache(string $key): bool
    {
        $fullKey = $this->buildCacheKey($key);
        $cache = AiClient::getCache();
        if ($cache !== null) {
            return $cache->has($fullKey);
        }
        return array_key_exists($fullKey, $this->localCache);
    }
    /**
     * Gets a value from the cache, or computes and caches it if not present.
     *
     * @since 0.4.0
     *
     * @param string                 $key      The cache key suffix (will be appended to the base key).
     * @param callable               $callback The callback to compute the value if not cached.
     * @param int|\DateInterval|null $ttl      The TTL for the cache entry, or null for default.
     *                                         Ignored for local cache.
     * @return mixed The cached or computed value.
     */
    protected function cached(string $key, callable $callback, $ttl = null)
    {
        if ($this->hasCache($key)) {
            return $this->getCache($key);
        }
        $value = $callback();
        $this->setCache($key, $value, $ttl);
        return $value;
    }
    /**
     * Gets a value from the cache.
     *
     * @since 0.4.0
     *
     * @param string $key     The cache key suffix (will be appended to the base key).
     * @param mixed  $default The default value to return if the key does not exist.
     * @return mixed The cached value or the default value if not found.
     */
    protected function getCache(string $key, $default = null)
    {
        $fullKey = $this->buildCacheKey($key);
        $cache = AiClient::getCache();
        if ($cache !== null) {
            return $cache->get($fullKey, $default);
        }
        return $this->localCache[$fullKey] ?? $default;
    }
    /**
     * Sets a value in the cache.
     *
     * @since 0.4.0
     *
     * @param string                $key   The cache key suffix (will be appended to the base key).
     * @param mixed                 $value The value to cache.
     * @param int|\DateInterval|null $ttl   The TTL for the cache entry, or null for default. Ignored for local cache.
     * @return bool True on success, false on failure.
     */
    protected function setCache(string $key, $value, $ttl = null): bool
    {
        $fullKey = $this->buildCacheKey($key);
        $cache = AiClient::getCache();
        if ($cache !== null) {
            return $cache->set($fullKey, $value, $ttl);
        }
        $this->localCache[$fullKey] = $value;
        return \true;
    }
    /**
     * Invalidates all caches managed by this object.
     *
     * @since 0.4.0
     *
     * @return void
     */
    public function invalidateCaches(): void
    {
        foreach ($this->getCachedKeys() as $key) {
            $this->clearCache($key);
        }
    }
    /**
     * Clears a value from the cache.
     *
     * @since 0.4.0
     *
     * @param string $key The cache key suffix (will be appended to the base key).
     * @return bool True on success, false on failure.
     */
    protected function clearCache(string $key): bool
    {
        $fullKey = $this->buildCacheKey($key);
        $cache = AiClient::getCache();
        if ($cache !== null) {
            return $cache->delete($fullKey);
        }
        unset($this->localCache[$fullKey]);
        return \true;
    }
    /**
     * Builds the full cache key by combining the base key with the suffix.
     *
     * @since 0.4.0
     *
     * @param string $key The cache key suffix.
     * @return string The full cache key.
     */
    private function buildCacheKey(string $key): string
    {
        return $this->getBaseCacheKey() . '_' . $key;
    }
}
