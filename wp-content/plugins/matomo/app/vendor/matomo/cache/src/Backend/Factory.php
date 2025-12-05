<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL v3 or later
 *
 */
namespace Matomo\Cache\Backend;

use Matomo\Cache\Backend;
class Factory
{
    public function buildArrayCache()
    {
        return new \Matomo\Cache\Backend\ArrayCache();
    }
    public function buildFileCache($options)
    {
        return new \Matomo\Cache\Backend\File($options['directory']);
    }
    public function buildNullCache()
    {
        return new \Matomo\Cache\Backend\NullCache();
    }
    public function buildChainedCache($options)
    {
        $backends = array();
        foreach ($options['backends'] as $backendToBuild) {
            $backendOptions = array();
            if (array_key_exists($backendToBuild, $options)) {
                $backendOptions = $options[$backendToBuild];
            }
            $backends[] = $this->buildBackend($backendToBuild, $backendOptions);
        }
        return new \Matomo\Cache\Backend\Chained($backends);
    }
    public function buildRedisCache($options)
    {
        if (empty($options['unix_socket']) && (empty($options['host']) || empty($options['port']))) {
            throw new \InvalidArgumentException('RedisCache is not configured. Please provide at least a host and a port');
        }
        $timeout = 0.0;
        if (array_key_exists('timeout', $options)) {
            $timeout = $options['timeout'];
        }
        $redis = new \Redis();
        if (empty($options['unix_socket'])) {
            $redis->connect($options['host'], $options['port'], $timeout);
        } else {
            $redis->connect($options['unix_socket'], 0, $timeout);
        }
        if (!empty($options['password'])) {
            $redis->auth($options['password']);
        }
        if (array_key_exists('database', $options)) {
            $redis->select((int) $options['database']);
        }
        $redisCache = new \Matomo\Cache\Backend\Redis();
        $redisCache->setRedis($redis);
        return $redisCache;
    }
    /**
     * @param string $class
     * @param array $options
     *
     * @return Backend
     *
     * @throws Factory\BackendNotFoundException
     */
    public function buildDecorated($class, $options)
    {
        $backendToBuild = $options["backend"];
        $backendOptions = array();
        if (array_key_exists($backendToBuild, $options)) {
            $backendOptions = $options[$backendToBuild];
        }
        $backend = $this->buildBackend($backendToBuild, $backendOptions);
        return new $class($backend, $options);
    }
    /**
     * Build a specific backend instance.
     *
     * @param string $type The type of backend you want to create. Eg 'array', 'file', 'chained', 'null', 'redis'.
     * @param array $options An array of options for the backend you want to create.
     * @return Backend
     * @throws Factory\BackendNotFoundException In case the given type was not found.
     */
    public function buildBackend($type, array $options)
    {
        switch ($type) {
            case 'array':
                return $this->buildArrayCache();
            case 'file':
                return $this->buildFileCache($options);
            case 'chained':
                return $this->buildChainedCache($options);
            case 'null':
                return $this->buildNullCache();
            case 'redis':
                return $this->buildRedisCache($options);
            case 'defaultTimeout':
                return $this->buildDecorated(\Matomo\Cache\Backend\DefaultTimeoutDecorated::class, $options);
            case 'keyPrefix':
                return $this->buildDecorated(\Matomo\Cache\Backend\KeyPrefixDecorated::class, $options);
            default:
                throw new \Matomo\Cache\Backend\Factory\BackendNotFoundException("Cache backend {$type} not valid");
        }
    }
}
