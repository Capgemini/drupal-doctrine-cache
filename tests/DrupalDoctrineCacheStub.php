<?php

namespace Capgemini\CacheTest;

use Capgemini\Cache\DrupalDoctrineCache;

/**
 * Stub for Drupal's cache.inc functions.
 *
 * @class DrupalDoctrineCacheStub
 * @package Capgemini\CacheTest
 */
class DrupalDoctrineCacheStub extends DrupalDoctrineCache {
  use DrupalCacheAdapter;
}
