<?php

namespace Drupal\ws_small_y_statistics\Plugin\Validation\Constraint;

use Drupal\block_content\BlockContentInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates that the Small Y Statistics block has at least 2 items.
 *
 * @package Drupal\ws_small_y\Plugin\Validation\Constraint
 */
class MinStatisticsItemsValidator extends ConstraintValidator {

  const MIN_ITEMS_REQUIREMENT = 2;

  const ITEM_BLOCK_TYPE = 'small_y_statistics';

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint):void {

    if ($entity instanceof BlockContentInterface && $entity->bundle() === self::ITEM_BLOCK_TYPE) {
      $items = $entity->get('field_block_item')->referencedEntities();
      if (count($items) < self::MIN_ITEMS_REQUIREMENT) {
        $this->context->addViolation($constraint->message, ['%requirement' => self::MIN_ITEMS_REQUIREMENT]);
      }
    }
  }

}
