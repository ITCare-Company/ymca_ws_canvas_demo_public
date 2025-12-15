<?php

namespace Drupal\ws_promotion\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ws_promotion\RandomizerPromotion;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * A PromotionController class
 */
class PromotionController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Randomizer Promotion service.
   *
   * @var \Drupal\ws_promotion\RandomizerPromotion;
   */
  protected RandomizerPromotion $randomizerPromotion;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('ws_promotion.randomizer'),
    );
  }

  /**
   * Constructs an PromotionController object.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   * The entity type manager.
   * @param RandomizerPromotion $randomizer_promotion
   * Helper class for get random promotion
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RandomizerPromotion $randomizer_promotion) {
    $this->entityTypeManager = $entity_type_manager;
    $this->randomizerPromotion = $randomizer_promotion;
  }

  /**
   * Return content type promotion.
   *
   * @param $widget
   * @param $category
   * @return JsonResponse
   * Rendered html promotion
   */
  public function build($widget, $category) {
    $node = $this->randomizerPromotion->getPromo($category);
    if (!$node) {
      return new JsonResponse(['data' => '']);
    }
    $viewBuilder = $this->entityTypeManager->getViewBuilder('node');
    $renderArray = $viewBuilder->view($node, $widget);
    $html = \Drupal::service('renderer')->renderRoot($renderArray);
    return new JsonResponse(['data' => $html]);
  }

}
