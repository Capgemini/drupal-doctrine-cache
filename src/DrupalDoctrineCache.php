<?php
namespace Capgemini\Cache;

use Doctrine\Common\Cache\CacheProvider;

/**
 * Doctrine cache that uses the Drupal caching API for storage.
 */
class DrupalDoctrineCache extends CacheProvider {
  use DrupalCacheAdapter;

  const CACHE_PREFIX = "doctrine:";

  protected $cache_table = 'cache';

  /**
   * Generate a cache ID for the cache entry, based on the original ID.
   *
   * @param string $id
   * @return string
   */
  protected function getCacheId($id) {
    return self::CACHE_PREFIX . "{$id}";
  }

  /**
   * Set the cache table to be used by the caching API.
   *
   * @param string $cache_table
   */
  public function setCacheTable($cache_table) {
    $this->cache_table = $cache_table;
  }

  /**
   * Get the cache table used by the caching API.
   *
   * @return string
   */
  public function getCacheTable() {
    return $this->cache_table;
  }

  /**
   * {@inheritdoc}
   */
  protected function doFetch($id) {
    $entry = $this->cache_get($this->getCacheId($id), $this->cache_table);
    if (empty($entry) || !isset($entry->data)) {
      return FALSE;
    }

    return $entry->data;
  }

  /**
   * {@inheritdoc}
   */
  protected function doContains($id) {
    $entry = $this->doFetch($id);
    return !empty($entry);
  }

  /**
   * {@inheritdoc}
   */
  protected function doSave($id, $data, $lifeTime = 0) {
    // Doctrine defines NULL to mean permanent cache.
    if ($lifeTime === NULL) {
      $lifeTime = CACHE_PERMANENT;
    }
    elseif ($lifeTime > 0) {
      $lifeTime += time();
    }

    // Cache data, converting Doctrine lifetime to a unix timestamp for Drupal.
    $result = $this->cache_set($this->getCacheId($id), $data, $this->cache_table, $lifeTime);
    return empty($result) ? FALSE : TRUE;
  }

  /**
   * Deletes a cache entry.
   *
   * @param string $id The cache id.
   *
   * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
   */
  protected function doDelete($id) {
    $this->cache_clear_all($this->getCacheId($id), $this->cache_table);
    return TRUE;
  }

  /**
   * Flushes all cache entries.
   *
   * With Drupal, caches are not really cleared until cron is run.
   *
   * @return boolean TRUE if the cache entries were successfully flushed, FALSE otherwise.
   */
  protected function doFlush() {
    $this->cache_clear_all('*', $this->cache_table, TRUE);
    return TRUE;
  }

  /**
   * Not implemented in this cache.
   *
   * Original description :-
   *
   * {@inheritdoc}
   * @codeCoverageIgnore
   */
  protected function doGetStats() {
    return NULL;
  }
}
