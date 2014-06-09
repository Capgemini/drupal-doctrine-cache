# Drupal Doctrine Cache

This repository provides a Doctrine cache that will use the Drupal 6/7 caching API for storage.

It's fairly straightforward to use, just configure your EntityManager with a new instance of the cache.

```
$cache = new \Capgemini\Cache\DrupalDoctrineCache();
$entityManagerConfiguration->setMetaDataCacheImpl($cache);
$entityManagerConfiguration->setQueryCacheImpl($cache);
$entityManagerConfiguration->setResultCacheImpl($cache);
```

By default the cache will use the 'cache' table.  You can change this :-

```
$cache->setCacheTable('my_cache_table');
```

Obviously you need to ensure that the table exists and is usable by the Drupal caching API.
