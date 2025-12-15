<?php

namespace Drupal\ws_home_branch\Plugin\HomeBranchLibrary;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\openy_home_branch\HomeBranchLibraryBase;

/**
 * Defines the home branch library plugin for Branch page.
 *
 * @HomeBranchLibrary(
 *   id="hb_loc_selector_branch_lb_page",
 *   label = @Translation("Home Branch Page Layout Builder Selector"),
 *   entity="node"
 * )
 */
class HBLocSelectorBranchLBPage extends HomeBranchLibraryBase {

  use StringTranslationTrait;

  const NODE_TYPE = 'branch';

  /**
   * {@inheritdoc}
   */
  public function getLibrary() {
    return 'ws_home_branch/loc_selector_branch_page_override';
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowedForAttaching($variables) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $variables['node'] ?? NULL;
    if (!$node || !$node->hasField('layout_builder__layout')) {
      return FALSE;
    }
    // Skip nodes with optional layout builder usage with deactivated usage.
    if ($node->hasField('field_use_layout_builder')
      && !$node->field_use_layout_builder->value) {
      return FALSE;
    }
    return $node->getType() == self::NODE_TYPE && $variables['page'];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibrarySettings() {
    return [
      'placeholderSelector' => '.block-lb-branch-hours .branch-name',
      'selectedText' => $this->t('Preferred location'),
      'notSelectedText' => $this->t('Preferred location'),
    ];
  }

}
