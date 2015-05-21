<?php

/**
 * @file
 * Contains Capgemini\Cache\DrupalCacheAdapter
 */

namespace Capgemini\Cache;

/**
 * Wrapper for core's cache_* functions.
 *
 * This trait allows for the implementations to be swapped out in tests,
 * removing the dependency on a bootstrapped Drupal environment.
 *
 * @class DrupalCacheAdapter
 * @package Capgemini\Cache
 * @codeCoverageIgnore
 */
trait DrupalCacheAdapter {

 /**
  * Returns data from the persistent cache.
  *
  * Data may be stored as either plain text or as serialized data. cache_get
  * will automatically return unserialized objects and arrays.
  *
  * @param $cid
  *   The cache ID of the data to retrieve.
  * @param $bin
  *   The cache bin to store the data in. Valid core values are 'cache_block',
  *   'cache_bootstrap', 'cache_field', 'cache_filter', 'cache_form',
  *   'cache_menu', 'cache_page', 'cache_path', 'cache_update' or 'cache' for
  *   the default cache.
  *
  * @return
  *   The cache or FALSE on failure.
  *
  * @see cache_set()
  */
  public function cache_get($cid, $bin = 'cache') {
    return cache_get($cid, $bin);
  }

  /**
   * Stores data in the persistent cache.
   *
   * The persistent cache is split up into several cache bins. In the default
   * cache implementation, each cache bin corresponds to a database table by the
   * same name. Other implementations might want to store several bins in data
   * structures that get flushed together. While it is not a problem for most
   * cache bins if the entries in them are flushed before their expire time, some
   * might break functionality or are extremely expensive to recalculate. The
   * other bins are expired automatically by core. Contributed modules can add
   * additional bins and get them expired automatically by implementing
   * hook_flush_caches().
   *
   * The reasons for having several bins are as follows:
   * - Smaller bins mean smaller database tables and allow for faster selects and
   *   inserts.
   * - We try to put fast changing cache items and rather static ones into
   *   different bins. The effect is that only the fast changing bins will need a
   *   lot of writes to disk. The more static bins will also be better cacheable
   *   with MySQL's query cache.
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
    return cache_set($cid, $data, $bin, $expire);
  }

  /**
   * Expires data from the cache.
   *
   * If called with the arguments $cid and $bin set to NULL or omitted, then
   * expirable entries will be cleared from the cache_page and cache_block bins,
   * and the $wildcard argument is ignored.
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
    cache_clear_all($cid, $bin, $wildcard);
  }
}
