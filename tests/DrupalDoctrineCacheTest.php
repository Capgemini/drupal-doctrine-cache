<?php

namespace Capgemini\CacheTest;

define('CACHE_PERMANENT', 0);

/**
 * @class DrupalDoctrineCacheTest
 * @package Capgemini\CacheTest
 */
class DrupalDoctrineCacheTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers Capgemini\Cache\DrupalDoctrineCache::setCacheTable
   * @covers Capgemini\Cache\DrupalDoctrineCache::getCacheTable
   */
  public function testSetCacheTable() {
    $cache = new DrupalDoctrineCacheStub();
    $cache->setCacheTable('cache_test');
    $this->assertEquals('cache_test', $cache->getCacheTable());
  }

  /**
   * @covers Capgemini\Cache\DrupalDoctrineCache::doSave
   * @covers Capgemini\Cache\DrupalDoctrineCache::getCacheId
   */
  public function testCacheSet() {
    $cache = new DrupalDoctrineCacheStub();
    $cache->save('key', 'value');
    $this->assertEquals(1, $cache->getCallCount('cache_set:doctrine:[key][1],value,cache,*', TRUE));
    $cache->save('key', 'value', NULL);
    $this->assertEquals(2, $cache->getCallCount('cache_set:doctrine:[key][1],value,cache,0'));
    $cache->save('key', 'value', 60);
    $this->assertEquals(1, $cache->getCallCount('cache_set:doctrine:[key][1],value,cache,' . substr(time(), 0, 1) . '*', TRUE));
  }

  /**
   * @covers Capgemini\Cache\DrupalDoctrineCache::doFetch
   * @covers Capgemini\Cache\DrupalDoctrineCache::doContains
   * @covers Capgemini\Cache\DrupalDoctrineCache::getCacheId
   */
  public function testCacheGet() {
    $cache = new DrupalDoctrineCacheStub();
    $cache->fetch('key');
    $cache->contains('key');
    $this->assertEquals(2, $cache->getCallCount('cache_get:doctrine:[key][1],cache'));

    $cache->setHitsCache(TRUE);
    $result = $cache->fetch('key');
    $this->assertEquals('Cache was hit', $result);
  }

  /**
   * @covers Capgemini\Cache\DrupalDoctrineCache::doDelete
   * @covers Capgemini\Cache\DrupalDoctrineCache::doFlush
   * @covers Capgemini\Cache\DrupalDoctrineCache::getCacheId
   */
  public function testCacheClear() {
    $cache = new DrupalDoctrineCacheStub();
    $cache->delete('key');
    $cache->flushAll();
    $this->assertEquals(1, $cache->getCallCount('cache_clear_all:doctrine:[key][1],cache'));
    $this->assertEquals(1, $cache->getCallCount('cache_clear_all:*,cache,1'));
  }
}
