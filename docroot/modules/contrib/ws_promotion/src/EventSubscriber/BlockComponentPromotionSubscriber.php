<?php

namespace Drupal\ws_promotion\EventSubscriber;

use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * A subscriber added promotion category to block configuration.
 */
class BlockComponentPromotionSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = ['onBuildRender', 100];
    return $events;
  }

  /**
   * Add promotion_category and promotion_include to block configuration.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   The section component render event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    if (($promotion_category = $event->getComponent()->get('promotion_category')) ||
      $event->getComponent()->get('promotion_include')) {
      $build = $event->getBuild();
      $build['#configuration']['promotion_category'] = $promotion_category;
      $build['#configuration']['promotion_include'] = $event->getComponent()->get('promotion_include');
      $event->setBuild($build);
    }
  }

}
