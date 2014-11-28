<?php
/**
 * @file
 * Contains ServiceContainer
 */

use Drupal\service_container\DependencyInjection\CachedContainerBuilder;
use Drupal\service_container\DependencyInjection\ServiceProviderPluginManager;

/**
 * Static Service Container wrapper extension - initializes the container.
 */
class ServiceContainer extends Drupal {

  /**
   * Initializes the container.
   *
   * This can be safely called from hook_boot() because the container will
   * only be build if we have reached the DRUPAL_BOOTSTRAP_FULL phase.
   *
   * @return bool
   *   TRUE when the container was initialized, FALSE otherwise.
   */
  public static function init() {
    // If this is set already, just return.
    if (isset(static::$container)) {
      return TRUE;
    }

    $service_provider_manager = new ServiceProviderPluginManager();
    // This is an internal API, but we need the cache object.
    $cache = _cache_get_object('cache');

    $container_builder = new CachedContainerBuilder($service_provider_manager, $cache);

    if ($container_builder->isCached()) {
      static::$container = $container_builder->compile();
      // @todo Emit some kind of event that the container is initialized now.
      return TRUE;
    }

    // If we have not yet fully bootstrapped, we can't build the container.
    if (drupal_bootstrap(NULL, FALSE) != DRUPAL_BOOTSTRAP_FULL) {
      return FALSE;
    }

    // Rebuild the container.
    static::$container = $container_builder->compile();
    // @todo Emit some kind of event that the container is initialized now.

    return (bool) static::$container;
  }
}