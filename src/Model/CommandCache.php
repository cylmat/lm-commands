<?php 

namespace LmConsole\Model;

use RuntimeException;

/**
 * Provide a very light cache file for Console
 */
class CommandCache 
{
    protected $path;

    protected $fileName = 'lmconsole.cache';

    public function __construct(string $pathDir) 
    {
        $this->createDir($pathDir);
    }

    /**
     * @throws E_USER_WARNING 
     */
    public function createDir(string $pathDir): void
    {
        if(!(file_exists($pathDir) || @mkdir($pathDir))) {
            trigger_error("Directory $pathDir cannot be created.", E_USER_WARNING);
        }
        $this->path = $pathDir . '/' . $this->fileName;
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     * @param mixed  $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get()
    {
        if (!$this->has()) {
            return null;
        }
        return unserialize(file_get_contents($this->path));
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store. Must be serializable.
     * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set($value)
    {
        return file_put_contents($this->path, serialize($value));
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete()
    {
        if (file_exists($this->path)) {
            return unlink($this->path);
        }
        return false;
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        $this->set('');
    }
    
    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it, making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has()
    {
        if (file_exists($this->path)) {
            return true;
        }
        return false;
    }
}