<?php

namespace Drupal\lb_cards\EventSubscriber;

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
      'inline_block:lb_cards' => [
        'border_style_component',
        'text_alignment_component',
        'button_position_component',
        'button_fill_component',
      ],
    ];
  }

}
