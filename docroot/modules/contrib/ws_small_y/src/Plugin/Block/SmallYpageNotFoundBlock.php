<?php

namespace Drupal\ws_small_y\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Small Y Page Not Found Content block.
 *
 * @Block(
 *   id = "small_y_404",
 *   admin_label = @Translation("Small Y Not Found Content Block"),
 *   category = @Translation("Small Y blocks")
 * )
 */
class SmallYpageNotFoundBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new SmallYpageNotFoundBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $sitemap = $this->entityTypeManager
      ->getStorage('simple_sitemap')
      ->load('default');
    $sitemap_link = $sitemap
      ? Link::fromTextAndUrl($this->t('Site Map'), $sitemap->toUrl())->toString()
      : '';
    return [
      '#theme' => 'small_y_page_not_found',
      '#sitemap_link' => $sitemap_link,
      '#home_link' => base_path(),
    ];
  }

}
