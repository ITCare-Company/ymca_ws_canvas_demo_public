<?php

namespace Drupal\ws_promotion;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;


/**
 * Helper class for get random node.
 */
class RandomizerPromotion {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * Constructs an RandomizerPromotion service.
   *
   * @param EntityTypeManagerInterface $entity_type_manager
   * The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get random promotion content type by category.
   *
   * @param $category
   * ID content type activity node OR FALSE for any category.
   * @return false|NodeInterface
   */
  public function getPromo($category) {
    $weightedValues = $this->getWeightedValues($category);
    $weightCategory = $this->getRandomWeightedElement($weightedValues);
    $nodeStorage = $this->entityTypeManager->getStorage('node');

    if (!$weightCategory) {
      return FALSE;
    }
    $properties = [
      'type' => 'promo',
      'field_promo_priority' => $weightCategory,
    ];
    if ($category) {
      $properties['field_promo_category'] = $category;
    }
    $nodes = $nodeStorage->loadByProperties($properties);

    if (!$nodes) {
      return FALSE;
    }

    return $this->getRandomNode((array) $nodes);
  }

  /**
   * Get random node from the list.
   *
   * @param array $nodes
   * @return false|mixed
   */
  protected function getRandomNode(array $nodes) {
    return $nodes[array_rand($nodes)];
  }

  /**
   * getRandomWeightedElement()
   * Utility function for getting random values with weighting.
   * Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
   * An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
   * The return value is the array key, A, B, or C in this case.  Note that the values assigned
   * do not have to be percentages.  The values are simply relative to each other.  If one value
   * weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
   * chance of being selected.  Also note that weights should be integers.
   *
   * @param array $weightedValues
   */
  protected function getRandomWeightedElement(array $weightedValues) {
    if(empty($weightedValues)){
      return false;
    }
    $rand = random_int(1, (int) array_sum($weightedValues));

    foreach ($weightedValues as $value) {
      $rand -= $value;
      if ($rand <= 0) {
        return $value;
      }
    }
    return $weightedValues[0];
  }

  /**
   * Return list promotion category.
   *
   * @param $category
   * @return array
   */
  protected function getWeightedValues($category) {
    $nodeStorage = $this->entityTypeManager->getStorage('node');
    $query = $nodeStorage->getAggregateQuery()
      ->groupBy('field_promo_priority')
      ->condition('type', 'promo')
      ->condition('status', 1)
      ->accessCheck(TRUE);

    if ($category) {
      $query->condition('field_promo_category', $category, '=');
    }

    $result = $query->execute();
    $values = [];
    foreach ($result as $item) {
      $values[] = $item['field_promo_priority'];
    }
    ksort($values);
    return $values;
  }

}
