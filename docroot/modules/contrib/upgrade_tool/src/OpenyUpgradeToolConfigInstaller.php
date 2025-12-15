<?php

namespace Drupal\openy_upgrade_tool;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigInstaller;
use Drupal\Core\Config\ConfigInstallerInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Decorates ConfigInstaller to allow OpenY modules with duplicate configs.
 *
 * Replicates FeaturesConfigInstaller behavior - overrides
 * findPreExistingConfiguration() to skip PreExistingConfigException
 * for OpenY-related configs during installation.
 */
class OpenyUpgradeToolConfigInstaller extends ConfigInstaller {

  /**
   * The configuration installer.
   *
   * @var \Drupal\Core\Config\ConfigInstallerInterface
   */
  protected $configInstaller;

  /**
   * The upgrade log manager.
   *
   * @var \Drupal\openy_upgrade_tool\OpenyUpgradeLogManagerInterface
   */
  protected $upgradeLogManager;

  /**
   * Constructs the configuration installer.
   *
   * @param \Drupal\Core\Config\ConfigInstallerInterface $config_installer
   *   The configuration installer.
   * @param \Drupal\openy_upgrade_tool\OpenyUpgradeLogManagerInterface $upgrade_log_manager
   *   The upgrade log manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Config\StorageInterface $active_storage
   *   The active configuration storage.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typed_config
   *   The typed configuration manager.
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The configuration manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param string $install_profile
   *   The name of the currently active installation profile.
   * @param \Drupal\Core\Extension\ExtensionPathResolver $extension_path_resolver
   *   The extension path resolver.
   */
  public function __construct(
    ConfigInstallerInterface $config_installer,
    OpenyUpgradeLogManagerInterface $upgrade_log_manager,
    ConfigFactoryInterface $config_factory,
    StorageInterface $active_storage,
    TypedConfigManagerInterface $typed_config,
    ConfigManagerInterface $config_manager,
    EventDispatcherInterface $event_dispatcher,
    $install_profile,
    ExtensionPathResolver $extension_path_resolver
  ) {
    parent::__construct(
      $config_factory,
      $active_storage,
      $typed_config,
      $config_manager,
      $event_dispatcher,
      $install_profile,
      $extension_path_resolver
    );
    $this->configInstaller = $config_installer;
    $this->upgradeLogManager = $upgrade_log_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function findPreExistingConfiguration(StorageInterface $storage, array $previous_config_names = []) {
    // Override Drupal\Core\Config\ConfigInstaller::findPreExistingConfiguration()
    // to replicate FeaturesConfigInstaller behavior.
    // Allow config that already exists coming from OpenY modules.
    //
    // @todo This is a workaround for legacy duplicate configs in the distro.
    //   Future versions should use core config management properly and avoid
    //   shipping duplicate configs across multiple modules. This decorator
    //   should be removed once the distro is refactored.

    // Get list of all OpenY configs.
    $openy_configs = $this->upgradeLogManager->getOpenyConfigList();
    // Map array so we can use isset instead of in_array for faster access.
    $openy_configs = array_combine($openy_configs, $openy_configs);

    $existing_configuration = [];
    // Gather information about all the supported collections.
    $collection_info = $this->configManager->getConfigCollectionInfo();

    foreach ($collection_info->getCollectionNames() as $collection) {
      $config_to_create = array_keys($this->getConfigToCreate($storage, $collection));
      $active_storage = $this->getActiveStorages($collection);
      foreach ($config_to_create as $config_name) {
        if ($active_storage->exists($config_name)) {
          // Test if config is part of OpenY modules.
          if (!isset($openy_configs[$config_name])) {
            // Config is NOT from OpenY, add it to the exception list.
            $existing_configuration[$collection][] = $config_name;
          }
          else {
            // Config IS from OpenY, log that we're allowing the duplicate.
            \Drupal::logger('openy_upgrade_tool')->warning('OpenyUpgradeToolConfigInstaller: Allowing duplicate OpenY config: @config. This is a legacy workaround and should be fixed in the distro.', [
              '@config' => $config_name,
            ]);
          }
        }
      }
    }

    return $existing_configuration;
  }

  /**
   * Creates configuration in a collection based on the provided list.
   *
   * @param string $collection
   *   The configuration collection.
   * @param array $config_to_create
   *   An array of configuration data to create, keyed by name.
   */
  public function createConfiguration($collection, array $config_to_create) {
    return parent::createConfiguration($collection, $config_to_create);
  }

}
