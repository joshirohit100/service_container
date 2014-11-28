<?php
/**
 * @file
 * Contains \Drupal\service_container\DependencyInjection\Container
 */

namespace Drupal\service_container\DependencyInjection;

use ReflectionClass;
use RuntimeException;

/**
 * Container is a DI container that provides services to users of the class.
 *
 * @ingroup dic
 */
class Container implements ContainerInterface {

  /**
   * The parameters of the container.
   *
   * @var array
   */
  protected $parameters = array();

  /**
   * The service definitions of the container.
   *
   * @var array
   */
  protected $serviceDefinitions = array();

  /**
   * The instantiated services.
   *
   * @var array
   */
  protected $services = array();

  /**
   * The currently loading services.
   *
   * @var array
   */
  protected $loading = array();

  public function __construct(array $container_definition) {
    $this->parameters = $container_definition['parameters'];
    $this->serviceDefinitions = $container_definition['services'];
    $this->services['service_container'] = $this;
  }

  /**
   * {@inheritdoc}
   */
  public function get($name, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
    // This is wrapped in a protected method to allow to mark services private
    // in the future.
    return $this->getService($name, $invalidBehavior);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    $definition = isset($this->serviceDefinitions[$plugin_id]) ? $this->serviceDefinitions[$plugin_id] : NULL;

    if (!$definition && $exception_on_invalid) {
      throw new RuntimeException(sprintf('The "%s" service definition does not exist.', $plugin_id));
    }

    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    return $this->serviceDefinitions;
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($plugin_id) {
    return (bool) $this->getDefinition($plugin_id, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function getParameter($name) {
    return isset($this->parameters[$name]) ? $this->parameters[$name] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function hasParameter($name) {
    return isset($this->parameters[$name]);
  }

  /**
   * Gets and instantiates a service from the Container.
   *
   * @param string $name
   *   The name of the service to retrieve.
   * @param int $invalidBehavior
   *   The behavior when the service does not exist
   *
   * @return object|bool
   *   The fully instantiated service object or FALSE if not found.
   */
  protected function getService($name, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
    if (isset($this->services[$name])) {
      return $this->services[$name];
    }

    if (isset($this->loading[$name])) {
      throw new RuntimeException(sprintf('Circular reference detected for service "%s", path: "%s".', $name, implode(' -> ', array_keys($this->loading))));
    }

    $definition = $this->getDefinition($name, $invalidBehavior === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE);

    if (!$definition) {
      $this->services[$name] = FALSE;
      return FALSE;
    }

    $this->loading[$name] = TRUE;

    $definition += array(
      'class' => '',
      'factory_class' => '',
      'factory_method' => '',
      'factory_service' => '',
      'arguments' => array(),
      'calls' => array(),
      'tags' => array(),
    ); // @codeCoverageIgnore

    try {
      $arguments = $this->expandArguments($definition['arguments'], $invalidBehavior);

      if (!empty($definition['factory_method'])) {
        $method = $definition['factory_method'];

        if (!empty($definition['factory_class'])) {
          $factory_class = $definition['factory_class'];
	  $factory = new $factory_class();
        }
        elseif (!empty($definition['factory_service'])) {
          $factory = $this->getService($definition['factory_service'], $invalidBehavior);
        }
        else {
          throw new RuntimeException(sprintf('Cannot create service "%s" from factory method without a factory service or factory class.', $name));
        }
        $service = call_user_func_array(array($factory, $method), $arguments);
      }
      else {
        // @todo Allow dynamic class definitions via parameters.
        $class = $definition['class'];
        $r = new ReflectionClass($class);

        $service = ($r->getConstructor() === NULL) ? $r->newInstance() : $r->newInstanceArgs($arguments);
      }
    }
    catch (\Exception $e) {
      unset($this->loading[$name]);
      throw $e;
    }
    unset($this->loading[$name]);

    foreach ($definition['calls'] as $call) {
      $method = $call[0];
      $arguments = array();
      if (!empty($call[1])) {
        $arguments = $this->expandArguments($call[1], $invalidBehavior);
      }
      call_user_func_array(array($service, $method), $arguments);
    }

    $this->services[$name] = $service;

    return $this->services[$name];
  }

  /**
   * Expands arguments from %parameter and @service to the resolved values.
   *
   * @param array $arguments
   *   The arguments to expand.
   * @param int $invalidBehavior
   *   The behavior when the service does not exist
   *
   * @return array
   *   The expanded arguments.
   *
   * @throws Exception if a parameter/service could not be resolved.
   */
  protected function expandArguments($arguments, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
    foreach ($arguments as $key => $argument) {
      if (!is_string($argument)) {
        continue;
      }

      if (strpos($argument, '%') === 0) {
        $name = substr($argument, 1, -1);
        if (!isset($this->parameters[$name])) {
          if ($invalidBehavior === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
            throw new RuntimeException("Could not find parameter: $name");
          }
	  $arguments[$key] = FALSE;
          continue;
        }
        $arguments[$key] = $this->parameters[$name];
      }
      else if (strpos($argument, '@') === 0) {
        $name = substr($argument, 1);
        if (!isset($this->serviceDefinitions[$name])) {
          if ($invalidBehavior === ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) {
            throw new RuntimeException("Could not find service: $name");
          }
	  $arguments[$key] = FALSE;
          continue;
        }
        $arguments[$key] = $this->getService($name, $invalidBehavior);
      }
    }

    return $arguments;
  }
}