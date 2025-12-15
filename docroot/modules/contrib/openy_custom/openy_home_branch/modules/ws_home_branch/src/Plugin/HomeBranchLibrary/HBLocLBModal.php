<?php

namespace Drupal\ws_home_branch\Plugin\HomeBranchLibrary;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\openy_home_branch\HomeBranchLibraryBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the home branch library plugin for modal with locations.
 *
 * @HomeBranchLibrary(
 *   id="hb_loc_lb_modal",
 *   label = @Translation("Home Branch Locations Modal for Layout Builder"),
 *   entity="block"
 * )
 */
class HBLocLBModal extends HomeBranchLibraryBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Creates plugin instance.
   *
   * @param array $configuration
   *   Plugin id.
   * @param string $plugin_id
   *   Plugin definition.
   * @param mixed $plugin_definition
   *   RouteMatch service instance.
   * @param $config_factory
   *   The config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrary() {
    return 'openy_home_branch/ws_location_modal';
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowedForAttaching($variables) {
    return ($variables['plugin_id'] == HBHeaderLBSelector::BLOCK_ID);
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrarySettings() {
    $config = $this->configFactory->get('openy_home_branch.settings');
    $title = $config->get('popup_title') ?? $this->t('Set preferred location');
    $description = $config->get('popup_description');
    $learn_more = $config->get('popup_learn_more');
    $delay = $config->get('popup_delay');

    return [
      'modalTitle' => $title,
      'modalDescription' => $description,
      'modalDelay' => $delay,
      'learnMoreText' => $learn_more['value'],
    ];
  }

}
