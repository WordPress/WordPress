<?php

namespace Doctrine\Common\Cache;

/**
 * Interface for cache drivers that supports multiple items manipulation.
 *
 * @link   www.doctrine-project.org
 */
interface MultiOperationCache extends \Doctrine\Common\Cache\MultiGetCache, \Doctrine\Common\Cache\MultiDeleteCache, \Doctrine\Common\Cache\MultiPutCache
{
}
