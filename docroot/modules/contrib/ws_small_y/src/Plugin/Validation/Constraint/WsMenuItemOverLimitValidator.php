<?php

namespace Drupal\ws_small_y\Plugin\Validation\Constraint;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates menu_link_content entities to check if item limits are exceeded..
 *
 * @package Drupal\ws_small_y\Plugin\Validation\Constraint
 */
class WsMenuItemOverLimitValidator extends ConstraintValidator implements ContainerInjectionInterface {

  const MENU_FIRST_LEVEL_LIMIT = 5;

  /**
   * A config object for the system performance configuration.
   */
  protected ImmutableConfig $config;

  /**
   * The menu link tree service.
   */
  protected MenuLinkTreeInterface $menuTree;

  /**
   * Constructs a WsMenuItemOverLimitValidator object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *    A config factory for retrieving required config objects.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_tree
   *    The menu tree service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    MenuLinkTreeInterface $menu_tree
  ) {
    $this->config = $config_factory->get('ws_small_y.settings');
    $this->menuTree = $menu_tree;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('menu.link_tree'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint):void {

    if ($entity instanceof MenuLinkContent) {
      // Skip limitation for a child menu link.
      $is_child = !empty($entity->getParentId());
      $menu_name = $entity->getMenuName();
      if ($menu_name !==  $this->config->get('main_menu') || $is_child) {
        return;
      }

      $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);
      $tree = $this->menuTree->load($menu_name, $parameters);
      // Add a violation if the amount of items has already been reached or
      // would exceed if the new item would be added to the menu.
      if (count($tree) > self::MENU_FIRST_LEVEL_LIMIT) {
        $this->context->addViolation($constraint->message, ['%limit' => self::MENU_FIRST_LEVEL_LIMIT]);
      }
    }
  }

}
