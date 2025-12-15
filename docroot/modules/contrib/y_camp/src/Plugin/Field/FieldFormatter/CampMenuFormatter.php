<?php

namespace Drupal\y_camp\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'y_camp_menu' formatter.
 *
 * Renders a menu entity reference as a menu tree for camp blocks.
 *
 * @FieldFormatter(
 *   id = "y_camp_menu",
 *   label = @Translation("Camp menu tree"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class CampMenuFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected MenuLinkTreeInterface $menuLinkTree;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->menuLinkTree = $container->get('menu.link_tree');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $menu_name = $item->target_id;
      if (empty($menu_name)) {
        continue;
      }

      // Build the menu tree.
      $parameters = new MenuTreeParameters();
      $parameters->setMaxDepth(3);

      $tree = $this->menuLinkTree->load($menu_name, $parameters);

      // Transform the tree.
      $manipulators = [
        ['callable' => 'menu.default_tree_manipulators:checkAccess'],
        ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
      ];
      $tree = $this->menuLinkTree->transform($tree, $manipulators);

      // Build the render array.
      $elements[$delta] = $this->menuLinkTree->build($tree);
      $elements[$delta]['#menu_name'] = $menu_name;
    }

    return $elements;
  }

}
