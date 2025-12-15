<?php

declare(strict_types=1);

namespace Drupal\daterange_compact\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\Plugin\Field\FieldType\TimestampItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\daterange_compact\DateRangeCompactFormatterInterface;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Compact' formatter for 'daterange' fields.
 *
 * This formatter renders the data range using <time> elements, with
 * configurable date formats (from the list of configured formats) and a
 * separator.
 *
 * @FieldFormatter(
 *   id = "daterange_compact",
 *   label = @Translation("Compact"),
 *   field_types = {
 *     "datetime",
 *     "daterange",
 *     "timestamp",
 *   }
 * )
 */
class DateRangeCompactFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The compact date range formatter service.
   */
  protected DateRangeCompactFormatterInterface $formatter;

  /**
   * The config entity storage for compact date range formats.
   */
  protected EntityStorageInterface $formatStorage;

  /**
   * Constructs a new DateRangeCompactFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\daterange_compact\DateRangeCompactFormatterInterface $formatter
   *   The compact date range formatter service.
   * @param \Drupal\Core\Entity\EntityStorageInterface $format_storage
   *   The compact date range format entity storage.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, DateRangeCompactFormatterInterface $formatter, EntityStorageInterface $format_storage) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->formatter = $formatter;
    $this->formatStorage = $format_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('daterange_compact.formatter'),
      $container->get('entity_type.manager')->getStorage('daterange_compact_format')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'daterange_compact_format' => 'medium_date',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $format_types = $this->formatStorage->loadMultiple();
    $options = [];
    foreach ($format_types as $type => $type_info) {
      $options[$type] = $type_info->label();
    }

    $form['daterange_compact_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Format'),
      '#description' => $this->t('Choose a compact format for displaying the date range.'),
      '#options' => $options,
      '#default_value' => $this->getSetting('daterange_compact_format'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Format: @format', ['@format' => $this->getSetting('daterange_compact_format')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    $format = $this->getSetting('daterange_compact_format');

    foreach ($items as $delta => $item) {
      foreach ($items as $delta => $item) {
        if ($item instanceof DateRangeItem) {
          $this->buildElementForDateRangeItem($elements, $delta, $item, $format);
        }
        elseif ($item instanceof DateTimeItem) {
          $this->buildElementForDateTimeItem($elements, $delta, $item, $format);
        }
        elseif ($item instanceof TimestampItem) {
          $this->buildElementForTimestampItem($elements, $delta, $item, $format);
        }
      }
    }

    return $elements;
  }


  /**
   * Add output for a single 'daterange' field item.
   */
  protected function buildElementForDateRangeItem(array &$elements, int $delta, DateRangeItem $item, string $format): void {
    if (!empty($item->start_date)) {

      // Date only range.
      if ($this->getFieldSetting('datetime_type') == DateTimeItem::DATETIME_TYPE_DATE) {
        $timezone = DateTimeItemInterface::STORAGE_TIMEZONE;
        $start_date = $item->value;
        $end_date = empty($item->end_value) ? $start_date : $item->end_value;
        $elements[$delta] = [
          '#plain_text' => (string) $this->formatter->formatDateRange($start_date, $end_date, $format, $timezone),
        ];
      }

      // Date+time range.
      else {
        $start_timestamp = $item->start_date->getTimestamp();
        $end_timestamp = empty($item->end_date) ? $start_timestamp : $item->end_date->getTimestamp();
        $timezone = date_default_timezone_get();
        $elements[$delta] = [
          '#plain_text' => (string) $this->formatter->formatTimestampRange($start_timestamp, $end_timestamp, $format, $timezone),
          '#cache' => [
            'contexts' => [
              'timezone',
            ],
          ],
        ];
      }

    }
  }

  /**
   * Add output for a single 'datetime' field item.
   */
  protected function buildElementForDateTimeItem(array &$elements, int $delta, DateTimeItem $item, string $format): void {
    if (!$item->isEmpty()) {

      // Non-range, date only.
      if ($this->getFieldSetting('datetime_type') == DateTimeItem::DATETIME_TYPE_DATE) {
        $timezone = DateTimeItemInterface::STORAGE_TIMEZONE;
        $start_date = $item->value;
        $end_date = $item->value;
        $elements[$delta] = [
          '#plain_text' => (string) $this->formatter->formatDateRange($start_date, $end_date, $format, $timezone),
        ];
      }

      // Non-range, date+time.
      else {
        $start_timestamp = $item->date->getTimestamp();
        $end_timestamp = $item->date->getTimestamp();
        $timezone = date_default_timezone_get();
        $elements[$delta] = [
          '#plain_text' => (string) $this->formatter->formatTimestampRange($start_timestamp, $end_timestamp, $format, $timezone),
          '#cache' => [
            'contexts' => [
              'timezone',
            ],
          ],
        ];
      }

    }
  }

  /**
   * Add output for a single 'timestamp' field item.
   */
  protected function buildElementForTimestampItem(array &$elements, int $delta, TimestampItem $item, string $format): void {
    if (!$item->isEmpty()) {
      $start_timestamp = (int) $item->value;
      $end_timestamp = (int) $item->value;
      $timezone = date_default_timezone_get();
      $elements[$delta] = [
        '#plain_text' => (string) $this->formatter->formatTimestampRange($start_timestamp, $end_timestamp, $format, $timezone),
        '#cache' => [
          'contexts' => [
            'timezone',
          ],
        ],
      ];
    }
  }


}
