<?php

/**
 * @file
 * Contains \Drupal\service_container\Tests\ServiceContainerCToolsIntegrationTest.
 */

namespace Drupal\service_container\Tests;

class ServiceContainerCToolsIntegrationTest extends ServiceContainerIntegrationTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp('service_container_test_ctools');

    drupal_flush_all_caches();

    // Once we have PR #22: https://github.com/LionsAd/service_container/pull/22
    // we'll remove these two following lines.
    \ServiceContainer::reset();
    \ServiceContainer::init();

    $this->container = \Drupal::getContainer();
  }

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ServiceContainerCToolsIntegration',
      'description' => 'Tests the \ServiceContainer class (CTools integration)',
      'group' => 'service_container',
    );
  }

  /**
   * Tests if CTools plugin types are available as services.
   */
  public function testCToolsPluginTypes() {
    foreach(ctools_plugin_get_plugin_type_info() as $module_name => $plugins) {
      foreach($plugins as $plugin_type => $plugin_data) {
        $services = array(
          $module_name . '.' . $plugin_type,
          $this->toStrToLower($module_name . '.' . $plugin_type),
          $this->toUnderscoreCase($module_name) . '.' . $this->toUnderscoreCase($plugin_type)
        );
        foreach($services as $service) {
          $this->assertTrue($this->container->has($service));
        }
      }
    }
  }

  /**
   * Tests if a particular CTools plugin is available.
   */
  public function testCToolsPlugin() {
    $service = \Drupal::service('service_container_test_ctools.ServiceContainerTestCtoolsPlugin')
      ->createInstance('ServiceContainerTestCtoolsPluginTest1');
    $this->assertEqual($service->beep(), 'beep!');

    $service = \Drupal::service('service_container_test_ctools.yolo')
      ->createInstance('yolo1');
    $this->assertEqual($service->beep(), 'beep!');
  }

  /**
   * Lowercase a UTF-8 string.
   *
   * @param $text
   *   The string to run the operation on.
   *
   * @return string
   *   The string in lowercase.
   *
   */
  public function toStrToLower($name) {
    return drupal_strtolower($name);
  }

  /**
   * Un-camelize a string.
   *
   * @param $text
   *   The string to run the operation on.
   *
   * @return string
   *   The string un-camelized.
   *
   */
  public function toUnderscoreCase($name) {
    return $this->toStrToLower(preg_replace('/(?<!^)([A-Z])/', '_$1', $name));
  }

}