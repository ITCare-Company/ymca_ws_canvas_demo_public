<?php

namespace Drupal\lb_grid_icon\EventSubscriber;

use Drupal\y_lb\WSStyleGroupAlterAbstract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * An event subscriber to alter available WS Style groups for a component.
 */
class WSStyleGroupAlter extends WSStyleGroupAlterAbstract implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  protected function getAllowedStyleGroups() {
    return [
      'inline_block:lb_icon_grid' => [
        'button_fill_component',
      ],
    ];
  }

}
