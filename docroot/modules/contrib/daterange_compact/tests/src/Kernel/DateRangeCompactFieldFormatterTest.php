<?php

declare(strict_types=1);

namespace Drupal\Tests\daterange_compact\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Tests compact date range field formatter functionality.
 *
 * These tests work by creating a couple of fields on an entity and rendering
 * those fields using the 'daterange_compact' formatter. They test the
 * behaviour of that field formatter, and the default configuration.
 *
 * More comprehensive testing of formatting logic can be found in
 * DateRangeCompactFormatterTest.
 *
 * Note: data is stored in UTC, but the default timezone when running tests
 * is Australia/Sydney (see https://www.drupal.org/node/2498619 for why).
 * Hence the discrepancy between 'value'/'end_value' and the expected
 * output.
 *
 * Australia/Sydney is UTC +10:00 (normal) or UTC +11:00 (DST)
 * DST starts first Sunday in October
 * DST ends first Sunday in April.
 *
 * @group field
 */
class DateRangeCompactFieldFormatterTest extends FieldFormatterTestBase {

  /**
   * Tests the display of an entity containing a date-only range field.
   *
   * @throws \Exception
   */
  public function testDateRangeField() {
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_date_range',
      'entity_type' => 'entity_test',
      'type' => 'daterange',
      'settings' => [
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATE,
      ],
    ]);
    $field_storage->save();

    $field_instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => 'Date range',
    ]);
    $field_instance->save();

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $display->setComponent('field_date_range', [
      'type' => 'daterange_compact',
      'settings' => [
        'daterange_compact_format' => 'medium_date',
      ],
    ]);
    $display->save();

    $entity = EntityTest::create([]);
    $entity->{'field_date_range'}->value = '2020-01-01';
    $entity->{'field_date_range'}->end_value = '2020-12-31';
    $this->renderEntityFields($entity, $display);

    $expected = '1 January – 31 December 2020';
    $message = 'Expecting the rendered entity to show "' . $expected . '"';
    $this->assertRaw($expected, $message);
  }

  /**
   * Tests the display of an entity containing a date and time range field.
   *
   * @throws \Exception
   */
  public function testDateTimeRangeField() {
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_date_time_range',
      'entity_type' => 'entity_test',
      'type' => 'daterange',
      'settings' => [
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME,
      ],
    ]);
    $field_storage->save();

    $field_instance = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'entity_test',
      'label' => 'Date & time range',
    ]);
    $field_instance->save();

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $display->setComponent('field_date_time_range', [
      'type' => 'daterange_compact',
      'settings' => [
        'daterange_compact_format' => 'medium_datetime',
      ],
    ]);
    $display->save();

    $entity = EntityTest::create([]);
    $entity->{'field_date_time_range'}->value = '2020-05-01T00:00:00';
    $entity->{'field_date_time_range'}->end_value = '2020-05-01T01:00:00';
    $this->renderEntityFields($entity, $display);

    $expected = '1 May 2020 10:00–11:00';
    $message = 'Expecting the rendered entity to show "' . $expected . '"';
    $this->assertRaw($expected, $message);
  }

}
