<?php

namespace Capgemini\CacheTest;

/**
 * Stub for testing calls to core's cache_* functions.
 *
 * @class DrupalCacheAdapter
 * @package Capgemini\CacheTest
 */
trait DrupalCacheAdapter {

  /**
   * Flag to determine if calls to cache_get() should hit the cache.
   *
   * @var bool
   */
  protected $hitsCache = FALSE;

  /**
   * An array of function calls and the number of times each one was called.
   *
   * @var array
   */
  protected $callCount = array();

  /**
   * Log a call to the cache_get() function.
   *
   * @param $cid
   *   The cache ID of the data to retrieve.
   * @param $bin
   *   The cache bin to store the data in. Valid core values are 'cache_block',
   *   'cache_bootstrap', 'cache_field', 'cache_filter', 'cache_form',
   *   'cache_menu', 'cache_page', 'cache_path', 'cache_update' or 'cache' for
   *   the default cache.
   *
   * @return bool|\stdClass
   *   The cache or FALSE on failure.
   */
  public function cache_get($cid, $bin = 'cache') {
    $this->logFunctionCall(__FUNCTION__, func_get_args());
    if ($this->isHitsCache()) {
      $cache = new \stdClass();
      $cache->data = 'Cache was hit';
      return $cache;
    }

    return FALSE;
  }

  /**
   * Log a call to the cache_set() function.
   *
   * @param $cid
   *   The cache ID of the data to store.
   * @param $data
   *   The data to store in the cache. Complex data types will be automatically
   *   serialized before insertion. Strings will be stored as plain text and are
   *   not serialized. Some storage engines only allow objects up to a maximum of
   *   1MB in size to be stored by default. When caching large arrays or similar,
   *   take care to ensure $data does not exceed this size.
   * @param $bin
   *   (optional) The cache bin to store the data in. Valid core values are:
   *   - cache: (default) Generic cache storage bin (used for theme registry,
   *     locale date, list of simpletest tests, etc.).
   *   - cache_block: Stores the content of various blocks.
   *   - cache_bootstrap: Stores the class registry, the system list of modules,
   *     the list of which modules implement which hooks, and the Drupal variable
   *     list.
   *   - cache_field: Stores the field data belonging to a given object.
   *   - cache_filter: Stores filtered pieces of content.
   *   - cache_form: Stores multistep forms. Flushing this bin means that some
   *     forms displayed to users lose their state and the data already submitted
   *     to them. This bin should not be flushed before its expired time.
   *   - cache_menu: Stores the structure of visible navigation menus per page.
   *   - cache_page: Stores generated pages for anonymous users. It is flushed
   *     very often, whenever a page changes, at least for every node and comment
   *     submission. This is the only bin affected by the page cache setting on
   *     the administrator panel.
   *   - cache_path: Stores the system paths that have an alias.
   * @param $expire
   *   (optional) One of the following values:
   *   - CACHE_PERMANENT: Indicates that the item should never be removed unless
   *     explicitly told to using cache_clear_all() with a cache ID.
   *   - CACHE_TEMPORARY: Indicates that the item should be removed at the next
   *     general cache wipe.
   *   - A Unix timestamp: Indicates that the item should be kept at least until
   *     the given time, after which it behaves like CACHE_TEMPORARY.
   *
   * @see _update_cache_set()
   * @see cache_get()
   */
  public function cache_set($cid, $data, $bin = 'cache', $expire = CACHE_PERMANENT) {
    $args = func_get_args();
    $this->logFunctionCall(__FUNCTION__, $args);
  }

  /**
   * Log a call to cache_clear_all().
   *
   * @param $cid
   *   If set, the cache ID or an array of cache IDs. Otherwise, all cache entries
   *   that can expire are deleted. The $wildcard argument will be ignored if set
   *   to NULL.
   * @param $bin
   *   If set, the cache bin to delete from. Mandatory argument if $cid is set.
   * @param $wildcard
   *   If TRUE, the $cid argument must contain a string value and cache IDs
   *   starting with $cid are deleted in addition to the exact cache ID specified
   *   by $cid. If $wildcard is TRUE and $cid is '*', the entire cache is emptied.
   */
  public function cache_clear_all($cid = NULL, $bin = NULL, $wildcard = FALSE) {
    $this->logFunctionCall(__FUNCTION__, func_get_args());
  }

  /**
   * Get the call count for a given key.
   *
   * @param string $key
   *   The key to get the call count for.
   * @param boolean @wildcard
   *   Set to TRUE if $key contains a wildcard ('*') character.
   *
   * @return int
   *   The number of times $key has been called.
   */
  public function getCallCount($key, $wildcard = FALSE) {
    $mapped = $this->callCount;
    if ($wildcard) {
      $escaped = strtr($key, array('[' => '\[', ']' => '\]'));
      foreach ($this->callCount as $index => $value) {
        if (fnmatch($escaped, $index)) {
          return $value;
        }
      }
    }

    if (isset($mapped[$key])) {
      return $mapped[$key];
    }

    return 0;
  }

  /**
   * Generate the array key we use to log function calls.
   *
   * @param string $key
   *   A unique string representing the function, such as __FUNCTION__.
   * @param array $args
   *   An array of function arguments.
   *
   * @return string
   *   The array key to log calls against.
   */
  protected function getKey($key, $args) {
    return $key . ':' . implode(',', $args);
  }

  /**
   * Log a function call.
   *
   * @param string $key
   *   A string with a function and any context data.
   */
  protected function logKey($key) {
    if (!isset($this->callCount[$key])) {
      $this->callCount[$key] = 0;
    }
    $this->callCount[$key]++;
  }

  /**
   * Log a function call.
   *
   * @param string $function
   *   A unique string representing the function, such as __FUNCTION__.
   * @param array $args
   *   An array of function arguments.
   *
   */
  protected function logFunctionCall($function, $args) {
    $key = $this->getKey($function, $args);
    $this->logKey($key);
  }

  /**
   * Flag to determine if calls to cache_get() will hit.
   *
   * @return boolean
   *   TRUE if cache_get() will return an object, FALSE otherwise.
   */
  public function isHitsCache() {
    return $this->hitsCache;
  }

  /**
   * Set if calls to cache_get() will hit.
   *
   * @param boolean $hitsCache
   *   TRUE if cache_get() should hit and return an object, FALSE otherwise.
   */
  public function setHitsCache($hitsCache) {
    $this->hitsCache = $hitsCache;
  }
}
