<?php

namespace PlacetoPay\BancolombiaSDK\Helpers;

use Psr\SimpleCache\CacheInterface;

class Cache
{
    protected $cache;

    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get($key, $default = null)
    {
        if ($this->cache) {
            return $this->cache->get(self::cacheTag($key), $default);
        }
        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set($key, $value, $ttl = null)
    {
        if ($this->cache) {
            return $this->cache->set(self::cacheTag($key), $value, $ttl);
        }
        return false;
    }

    /**
     * @param string $key
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function has($key)
    {
        if ($this->cache) {
            return $this->cache->has(self::cacheTag($key));
        }
        return false;
    }

    /**
     * @param string $key
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete($key)
    {
        if ($this->cache) {
            return $this->cache->delete(self::cacheTag($key));
        }
        return false;
    }

    public function cacheTag($identificator)
    {
        return 'bancolombia.token.' . hash('sha256', $identificator);
    }
}
