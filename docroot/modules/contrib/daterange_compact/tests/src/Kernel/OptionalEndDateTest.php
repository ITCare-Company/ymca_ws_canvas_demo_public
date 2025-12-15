<?php

declare(strict_types=1);

namespace Drupal\Tests\daterange_compact\Kernel;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Tests the use of the 'optional end date' module with date-only ranges.
 */
class OptionalEndDateTest extends FieldFormatterTestBase {

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
      'field_name' => 'field_date_range',
      'entity_type' => 'entity_test',
      'type' => 'daterange',
      'settings' => [
        'datetime_type' => DateTimeItem::DATETIME_TYPE_DATE,
        'optional_end_date' => TRUE,
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
  }

  /**
   * Test value with an end date.
   */
  public function testValueWithEndDate() {
    $entity = EntityTest::create([]);
    $entity->{'field_date_range'}->value = '2024-08-22';
    $entity->{'field_date_range'}->end_value = '2024-08-23';

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $this->renderEntityFields($entity, $display);

    $expected = '22–23 August 2024';
    $message = 'Expecting the rendered entity to show "' . $expected . '"';
    $this->assertRaw($expected, $message);
  }

  /**
   * Test value without an end date.
   */
  public function testValueWithoutEndDate() {
    $entity = EntityTest::create([]);
    $entity->{'field_date_range'}->value = '2024-08-22';

    $display = EntityViewDisplay::load('entity_test.entity_test.default');
    $this->renderEntityFields($entity, $display);

    $expected = '22 August 2024';
    $message = 'Expecting the rendered entity to show "' . $expected . '"';
    $this->assertRaw($expected, $message);
  }

}
