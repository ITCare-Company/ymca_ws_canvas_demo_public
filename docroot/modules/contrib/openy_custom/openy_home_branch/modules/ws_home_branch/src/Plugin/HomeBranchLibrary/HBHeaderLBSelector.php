<?php

namespace Drupal\ws_home_branch\Plugin\HomeBranchLibrary;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\openy_home_branch\HomeBranchLibraryBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the home branch library plugin for Home Branch selector block.
 *
 * @HomeBranchLibrary(
 *   id="hb_header_lb_selector",
 *   label = @Translation("Home Branch Header Layout Builder Selector"),
 *   entity="block"
 * )
 */
class HBHeaderLBSelector extends HomeBranchLibraryBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  const BLOCK_ID = 'ws_site_name';

  /**
   * The Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Branches list.
   *
   * @var array
   */
  protected $branchesList = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->branchesList = $this->getBranches();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrary() {
    return 'ws_home_branch/hb_header_lb_selector';
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowedForAttaching($variables) {
    return ($variables['plugin_id'] == self::BLOCK_ID);
  }

  /**
   * Get branches list.
   */
  public function getBranches() {
    $query = $this->database->select('node_field_data', 'n');
    $query->fields('n', ['nid', 'title']);
    $query->condition('n.status', NodeInterface::PUBLISHED);
    $query->condition('n.type', 'branch');
    $query->orderBy('n.title');
    $query->addTag('openy_home_branch_get_locations');
    $query->addTag('node_access');
    return $query->execute()->fetchAllKeyed();
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrarySettings() {
    return [
      'targetSelector' => '.ws-header .header--top-left-column .block-ws-site-name',
      'defaultTitle' => $this->t('Set preferred location'),
      'locations' => $this->branchesList,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = ['node_list'];
    foreach (array_keys($this->branchesList) as $id) {
      $tags[] = 'node:' . $id;
    }

    return $tags;
  }

}
