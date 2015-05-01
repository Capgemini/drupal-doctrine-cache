<?php

namespace Capgemini\CacheTest;

trait DrupalCacheAdapter {

  protected $hitsCache = FALSE;

  protected $callCount = array();

  public function cache_get($cid, $bin = 'cache') {
    $this->logMethodCall(__FUNCTION__, func_get_args());
    if ($this->isHitsCache()) {
      $cache = new \stdClass();
      $cache->data = 'Cache was hit';
      return $cache;
    }

    return FALSE;
  }

  public function cache_set($cid, $data, $bin = 'cache', $expire = CACHE_PERMANENT) {
    $args = func_get_args();
    $this->logMethodCall(__FUNCTION__, $args);
  }

  public function cache_clear_all($cid = NULL, $bin = NULL, $wildcard = FALSE) {
    $this->logMethodCall(__FUNCTION__, func_get_args());
  }

  /**
   * Get the call count for a given key.
   *
   * @param string $key
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
   * @param $key
   * @param $args
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
   * @param $function
   * @param $args
   */
  protected function logMethodCall($function, $args) {
    $key = $this->getKey($function, $args);
    $this->logKey($key);
  }

  /**
   * @return boolean
   */
  public function isHitsCache() {
    return $this->hitsCache;
  }

  /**
   * @param boolean $hitsCache
   */
  public function setHitsCache($hitsCache) {
    $this->hitsCache = $hitsCache;
  }
}
