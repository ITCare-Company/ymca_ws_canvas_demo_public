<?php

namespace Drupal\ws_promotion_cards\EventSubscriber;

use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A subscriber added block option style to block configuration.
 */
class BlockComponentVariationSubscriber implements EventSubscriberInterface {


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = ['onBuildRender', 100];
    return $events;
  }

  /**
   * Add each component's block styles to the render array.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   The section component render event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    if (($bootstrap_styles = $event->getComponent()->get('bootstrap_styles')) &&
      isset($bootstrap_styles['block_style']['ws_style_option']['lb_cards'])) {
      $build = $event->getBuild();
      $build['#configuration']['block_variation'] = $bootstrap_styles['block_style']['ws_style_option']['lb_cards'];
      $event->setBuild($build);
    }
  }

}
