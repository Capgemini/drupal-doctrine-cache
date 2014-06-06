<?php
namespace Capgemini\Cache;

use Doctrine\Common\Cache\Cache;

/**
 * Doctrine cache that uses the Drupal caching API for storage.
 */
class DrupalDoctrineCache implements Cache {
  const CACHE_PREFIX = "doctrine:";

  protected $cache_table = 'cache';

  /**
   * Fetches a cache entry from a Drupal cache.
   *
   * @inheritdoc
   */
  public function fetch($id) {
    $entry = cache_get($this->getCacheId($id), $this->cache_table);
    if (empty($entry) || !isset($entry->data)) {
      return FALSE;
    }

    return $entry->data;
  }

  /**
   * Checks if the Drupal cache contains the specified entry.
   *
   * @inheritdoc
   */
  public function contains($id) {
    $entry = $this->fetch($id);
    return !empty($entry);
  }

  /**
   * Saves data to the Drupal cache.
   *
   * @inheritdoc
   */
  public function save($id, $data, $lifeTime = NULL) {
    // Doctrine defines NULL to mean permanent cache.
    if ($lifeTime == NULL) {
      $lifeTime = CACHE_PERMANENT;
    }
    else {
      $lifeTime += time();
    }

    // Cache data, converting Doctrine lifetime to a unix timestamp for Drupal.
    $result = cache_set($this->getCacheId($id), $data, $this->cache_table, $lifeTime);
    return empty($result) ? FALSE : TRUE;
  }

  /**
   * Deletes the entry from the Drupal cache.
   *
   * @inheritdoc
   */
  public function delete($id) {
    cache_clear_all($this->getCacheId($id), $this->cache_table);
    return TRUE;
  }

  /**
   * Not implemented in this cache.
   *
   * Original description :-
   *
   * @inheritdoc
   *
   */
  public function getStats() {
    // We don't implement this.
    return NULL;
  }

  /**
   * Generate a cache ID for the cache entry, based on the original ID.
   *
   * @param $id
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

} 