<?php

namespace App\Support;

use Aws\CacheInterface;
use Illuminate\Cache\CacheManager;

/**
 * Class LaravelCacheAdapter
 *
 * @package FanLat\Support
 */
class LaravelCacheAdapter implements CacheInterface
{
	const PREFIX = 'aws_credentials_';

	/**
	 * @var CacheManager
	 */
	private $manager;

	/**
	 * LaravelCacheAdapter constructor.
	 *
	 * @param CacheManager $cacheManager
	 */
	public function __construct(CacheManager $cacheManager)
	{
		$this->manager = $cacheManager;
	}
	
	/**
	 * Get a cache item by key.
	 *
	 * @param string $key Key to retrieve.
	 *
	 * @return mixed|null Returns the value or null if not found.
	 */
	public function get($key)
	{
		return $this->cache()->get($this->generateKey($key));
	}

	/**
	 * Set a cache key value.
	 *
	 * @param string $key   Key to set
	 * @param mixed $value  Value to set.
	 * @param int $ttl      Number of seconds the item is allowed to live. Set
	 *                      to 0 to allow an unlimited lifetime.
	 */
	public function set($key, $value, $ttl = 0)
	{
		$this->cache()->put($this->generateKey($key), $value, $ttl);
	}

	/**
	 * Remove a cache key.
	 *
	 * @param string $key Key to remove.
	 */
	public function remove($key)
	{
		$this->cache()->forget($this->generateKey($key));
	}

	/**
	 * @return \Illuminate\Contracts\Cache\Repository
	 */
	protected function cache()
	{
		return $this->manager->store('file');
	}

	/**
	 * The AWS CacheInterface takes input in seconds, but the Laravel Cache classes use minutes. To support
	 * this intelligently, we round up to one minute for any value less than 60 seconds, and round down to
	 * the nearest whole minute for any value over one minute.
	 *
	 * @param $ttl
	 * @return float|int
	 */
	protected function convertTtl($ttl)
	{
		if ($ttl === 0) return 0;

		$minutes = floor($ttl / 60);
		if ($minutes == 0) {
			return 1;
		} else {
			return $minutes;
		}
	}

	/**
	 * Generate a cache key which incorporates the prefix.
	 *
	 * @param $key
	 * @return string
	 */
	protected function generateKey($key)
	{
		return $key;
	}
}