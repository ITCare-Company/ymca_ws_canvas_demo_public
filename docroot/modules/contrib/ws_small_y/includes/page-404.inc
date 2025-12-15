<?php

/**
 * @file
 * Contains functions for set custom Page Not Found for the YMCA WS - Small Y.
 */

use Drupal\node\Entity\Node;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\node\NodeInterface;

/**
 * Set Small Y Page Not Found page as default for the site.
 */
function _ws_small_set_404_page() {
  $node_404 = _ws_small_create_404_page();
  if (!$node_404) {
    return;
  }

  $url = $node_404->toUrl();
  $page_404_path = $url ? $url->getInternalPath() : '';
  $site_config = \Drupal::configFactory()->getEditable('system.site');
  $site_config->set('page.404', '/' . $page_404_path);
  $site_config->save();
}

/**
 * Creates a new LP node with PageNotFoundContent Block.
 *
 * @return \Drupal\node\NodeInterface $node
 *   The created LP node.
 */
function _ws_small_create_404_page(): NodeInterface {
  $view_display = \Drupal::entityTypeManager()
    ->getStorage('entity_view_display')
    ->load('node.landing_page_lb.default');
  $styles_settings = $view_display->getThirdPartySettings('y_lb');
  $styles_settings['styles']['colorway'] = 'ws_colorway_green';
  $node = Node::create([
    'uid' => '1',
    'type' => 'landing_page_lb',
    'title' => '404',
    'status' => 1,
    'promote' => 0,
    'sticky' => 0,
    'created' => Drupal::time()->getRequestTime(),
    'changed' => Drupal::time()->getRequestTime(),
    'path' => ['alias' => '/404'],
    'override_styles' => 1,
    'styles' => serialize($styles_settings['styles']),
  ]);

  $layout_builder_settings = $view_display->getThirdPartySettings('layout_builder');
  $layout_builder__layout = _ws_small_prepare_lb_sections($layout_builder_settings['sections']);
  $node->set('layout_builder__layout', $layout_builder__layout);
  $node->save();
  return $node;
}

/**
 * Process default section and adds a new section with PageNotFoundContent block.
 *
 * @param \Drupal\node\NodeInterface $node
 *  The created LP node.
 *
 * @return array
 *   An array with sections to set.
 */
function _ws_small_prepare_lb_sections(array $sections): array {
  $landing_page_layout = [];

  if (!$sections) {
    return [];
  }
  // We should put it in the correct order.
  foreach ($sections as $section) {
    $layoutId = $section->getLayoutId();
    if ($layoutId === 'ws_header') {
      $landing_page_layout[0] = $section;
    }
    $layout_settings = $section->getLayoutSettings();

    if ($layout_settings['label'] === 'Body') {
      $landing_page_layout[1] = _ws_small_y_create_page_not_found_section();
    }
    if ($layoutId === 'ws_footer') {
      $landing_page_layout[2] = $section;
    }
  }
  return $landing_page_layout;
}

/**
 * Creates a new section with PageNotFoundContent Block.
 *
 * @return \Drupal\layout_builder\Section
 *  A section which will be added to the created node.
 */
function _ws_small_y_create_page_not_found_section(): Section {
  $uuid_service = \Drupal::service('uuid');
  $section = new Section('bootstrap_layout_builder:blb_col_1', [
    'container' => 'w-100',
    'label' => 'Page Not Found',
    'remove_gutters' => '1',
    'container_wrapper' => [],
  ]);
  $component = new SectionComponent($uuid_service->generate(), 'blb_region_col_1', [
    'id' => 'small_y_404',
    'label' => 'Small Y Not Found Block',
    'label_display' => '0',
    'provider' => 'ws_small_y',
  ]);
  $section->appendComponent($component);

  return $section;
}
