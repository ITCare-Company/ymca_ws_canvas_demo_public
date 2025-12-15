<?php

namespace Drupal\config_import;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Class ConfigImportServiceProvider.
 */
class ConfigImportServiceProvider extends ServiceProviderBase {

  /**
   * DI container.
   *
   * @var ContainerBuilder
   */
  private $container;

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    $this->container = $container;
  }

}
