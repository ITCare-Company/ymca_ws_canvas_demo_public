<?php

declare(strict_types=1);

namespace Drupal\Tests\daterange_compact\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Tests the use of the 'optional end date' module with datetime ranges.
 */
class OptionalEndDateTimeTest extends FieldFormatterTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'optional_end_date',
  ];

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_datetime_range',
      'entity_type' => 'entity_test',
      'type' => 'daterange',
      'settings' => [
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME,
        'optional_end_date' => TRUE,
      ],
    ]);
    $field_storage->save();

    $field_instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => 'Datetime range',
    ]);
    $field_instance->save();

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $display->setComponent('field_datetime_range', [
      'type' => 'daterange_compact',
      'settings' => [
        'daterange_compact_format' => 'medium_datetime',
      ],
    ]);
    $display->save();
  }

  /**
   * Test value with an end date.
   */
  public function testValueWithEndDate() {
    $entity = EntityTest::create([]);
    $entity->{'field_datetime_range'}->value = '2024-08-23T08:00:00';
    $entity->{'field_datetime_range'}->end_value = '2024-08-23T10:00:00';

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $this->renderEntityFields($entity, $display);

    // 10 hours ahead of UTC, see DateRangeCompactFieldFormatterTest for why.
    $expected = '23 August 2024 18:00–20:00';
    $message = 'Expecting the rendered entity to show "' . $expected . '"';
    $this->assertRaw($expected, $message);
  }

  /**
   * Test value without an end date.
   */
  public function testValueWithoutEndDate() {
    $entity = EntityTest::create([]);
    $entity->{'field_datetime_range'}->value = '2024-08-23T08:00:00';

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $this->renderEntityFields($entity, $display);

    // 10 hours ahead of UTC, see DateRangeCompactFieldFormatterTest for why.
    $expected = '23 August 2024 18:00';
    $message = 'Expecting the rendered entity to show "' . $expected . '"';
    $this->assertRaw($expected, $message);
  }

}
