<?php

declare(strict_types=1);

namespace Drupal\Tests\daterange_compact\Kernel;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * Base class for testing the field formatter.
 *
 * Ensures that an 'entity_test' entity exists with a 'default' bundle
 * and a 'default' display. Subclasses can configure this how they wish.
 *
 * Provides a renderEntityFields() function that captures a rendered
 * entity as a string for comparison with expected values.
 */
abstract class FieldFormatterTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'field',
    'datetime',
    'datetime_range',
    'daterange_compact',
    'entity_test',
    'user',
  ];

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['system']);
    $this->installConfig(['field']);
    $this->installConfig(['daterange_compact']);
    $this->installEntitySchema('entity_test');

    EntityViewDisplay::create([
      'targetEntityType' => 'entity_test',
      'bundle' => 'entity_test',
      'mode' => 'default',
      'status' => TRUE,
    ])->save();
  }

  /**
   * Renders fields of a given entity with a given display.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity object with attached fields to render.
   * @param \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display
   *   The display to render the fields in.
   *
   * @return string
   *   The rendered entity fields.
   *
   * @throws \Exception
   */
  protected function renderEntityFields(FieldableEntityInterface $entity, EntityViewDisplayInterface $display): string {
    $content = $display->build($entity);
    $content = $this->render($content);
    return $content;
  }

}
