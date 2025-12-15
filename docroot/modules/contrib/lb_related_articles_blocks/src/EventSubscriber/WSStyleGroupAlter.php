<?php

namespace Drupal\lb_related_articles_blocks\EventSubscriber;

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
      'inline_block:lb_related_articles' => [
        'border_style_component',
        'text_alignment_component',
      ],
    ];
  }

}
