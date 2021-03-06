<?php

/**
 * @file
 * Contains \Drupal\service_container_symfony\ServiceContainer\ServiceProvider\ServiceContainerSymfonyServiceProvider
 */

namespace Drupal\service_container_symfony\ServiceContainer\ServiceProvider;

use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\YamlFileLoader;
use Drupal\service_container_symfony\DependencyInjection\Dumper\PhpArrayDumper;
use Drupal\service_container\DependencyInjection\ServiceProviderInterface;

/**
 * Provides render cache service definitions.
 *
 * @codeCoverageIgnore
 *
 */
class ServiceContainerSymfonyServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getContainerDefinition() {
    FileCacheFactory::setConfiguration(['default' => ['class' => '\Drupal\Component\FileCache\NullFileCache']]);
    $modules = module_list();
    $container_builder = new ContainerBuilder();
    $yaml_loader = new YamlFileLoader($container_builder);

    foreach ($modules as $module) {
      $filename = drupal_get_filename('module', $module);
      $services = dirname($filename) . "/$module.services.yml";
      if (file_exists($services)) {
        $yaml_loader->load($services);
      }
    }

    $container_builder->compile();
    $dumper = new PhpArrayDumper($container_builder);
    return $dumper->getArray();
  }

  /**
   * {@inheritdoc}
   */
  public function alterContainerDefinition(&$container_definition) {}

}
