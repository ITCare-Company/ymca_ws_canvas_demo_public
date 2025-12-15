<?php

namespace Drupal\y_branch\Entity;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay;
use Drupal\layout_builder\Entity\LayoutEntityDisplayInterface;

/**
 * Provides an entity view display entity that has a configurable layout option.
 */
class YBranchLayoutBuilderEntityViewDisplay extends LayoutBuilderEntityViewDisplay implements LayoutEntityDisplayInterface {

  /**
   * {@inheritdoc}
   */
  protected function buildSections(FieldableEntityInterface $entity) {
    if ($entity->hasField('field_use_layout_builder')
      && !$entity->field_use_layout_builder->value) {
      return [];
    }

    return parent::buildSections($entity);
  }

}
