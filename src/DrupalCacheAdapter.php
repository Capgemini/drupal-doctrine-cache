<?php

namespace Capgemini\Cache;

/**
 * @class DrupalCacheAdapter
 * @package Capgemini\Cache
 * @codeCoverageIgnore
 */
trait DrupalCacheAdapter {

  public function cache_get($cid, $bin = 'cache') {
    return cache_get($cid, $bin);
  }

  public function cache_set($cid, $data, $bin = 'cache', $expire = CACHE_PERMANENT) {
    cache_set($cid, $data, $bin, $expire);
  }

  public function cache_clear_all($cid = NULL, $bin = NULL, $wildcard = FALSE) {
    cache_clear_all($cid, $bin, $wildcard);
  }
}
