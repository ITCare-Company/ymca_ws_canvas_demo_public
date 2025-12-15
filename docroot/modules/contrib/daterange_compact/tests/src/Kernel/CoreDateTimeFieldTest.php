<?php

declare(strict_types=1);

namespace Drupal\Tests\daterange_compact\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

class CoreDateTimeFieldTest extends FieldFormatterTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_datetime',
      'entity_type' => 'entity_test',
      'type' => 'datetime',
      'settings' => [
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME,
      ],
    ]);
    $field_storage->save();

    $field_instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => 'Date',
    ]);
    $field_instance->save();

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $display->setComponent('field_datetime', [
      'type' => 'daterange_compact',
      'settings' => [
        'daterange_compact_format' => 'medium_datetime',
      ],
    ]);
    $display->save();
  }

  /**
   * Test a date only field value.
   */
  public function testFieldValue() {
    $entity = EntityTest::create();
    $entity->field_datetime->value = '2024-08-22T05:00:00';

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $this->renderEntityFields($entity, $display);

    // 10 hours ahead of UTC, see DateRangeCompactFieldFormatterTest for why.
    $expected = '22 August 2024 15:00';
    $message = 'Expecting the rendered entity to show "' . $expected . '"';
    $this->assertRaw($expected, $message);
  }

}
