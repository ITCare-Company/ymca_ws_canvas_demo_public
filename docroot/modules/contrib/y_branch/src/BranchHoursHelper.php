<?php

namespace Drupal\y_branch;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Helper service for getting data about Branch hours.
 */
class BranchHoursHelper {

  use StringTranslationTrait;

  /**
   * Offset to show field before a holiday. 14 days.
   */
  const SHOW_BEFORE_OFFSET = 1209600;

  /**
   * Offset to show field after a holiday. 1 day.
   */
  const SHOW_AFTER_OFFSET = 86400;

  /**
   * Contains the configuration object factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Constructs a new TrilliumHoursHelper.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The configuration factory object.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(ConfigFactoryInterface $config, TimeInterface $time) {
    $this->configFactory = $config;
    $this->time = $time;
  }

  /**
   * Returns array with data for rendering Branch Hours.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   *
   * @return array
   *   Branch hours data array.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getBranchHours(NodeInterface $node) {
    $js_settings = [];
    $rows = $table = [];
    $label = '';
    $weekday_names = $this->getWeekdayNames();

    $field_name = 'field_branch_hours';
    if ($node->hasField($field_name) && !$node->get($field_name)->isEmpty()) {
      $branch_hours = $node->{$field_name}->get(0)->getValue();
      unset($branch_hours['_attributes']);
      $groups = [];
      foreach ($branch_hours as $key => $i_item) {
        // Do not process label. Store it name for later usage.
        if ($key === 'hours_label') {
          $label = $i_item;
          continue;
        }

        $day = ucfirst(str_replace('hours_', '', $key));
        $value = $i_item ?: $this->t('Closed');
        $value_js = $weekday_names[$day] . '<br />' . $value;
        if ($groups && end($groups)['value'] == $value) {
          $array_keys = array_keys($groups);
          $group = &$groups[end($array_keys)];
          $group['days'][] = $day;
        }
        else {
          $groups[] = [
            'value' => $value,
            'days' => [$day],
          ];
        }

        // Populate JS hours.
        $js_settings[$day] = $value_js;
      }

      $date = DrupalDateTime::createFromTimestamp($this->time->getRequestTime(), $this->configFactory->get('system.date')->get('timezone')['default']);
      $today = $date->format('D');

      foreach ($groups as $group_item) {
        $class = in_array($today, $group_item['days'], true) ? 'current-day' : '';
        $title = sprintf('%s - %s', reset($group_item['days']), end($group_item['days']));
        if (count($group_item['days']) == 1) {
          $title = reset($group_item['days']);
        }

        $hours = $group_item['value'];
        $rows[] = ['data' => [$this->t($title), $hours], 'class' => $class];
      }

      if (!empty($rows)) {
        $table = [
          '#theme' => 'table',
          '#header' => [],
          '#rows' => $rows,
        ];
      }
    }

    return [
      'label' => $label,
      'table' => $table,
      'js_settings' => $js_settings,
    ];
  }

  /**
   * Returns array with data for rendering Branch Hours in Lazy Builder.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   *
   * @return array
   *   Branch hours data array.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getLazyBranchHours(NodeInterface $node) {
    $lazy_hours = [];
    $weekday_names = $this->getWeekdayNames();

    if ($node->hasField('field_branch_hours') && $node->field_branch_hours->get(0)) {
      $branch_hours = $node->field_branch_hours->get(0)->getValue();
      foreach ($branch_hours as $key => $i_item) {
        $day = ucfirst(str_replace('hours_', '', $key));
        if (empty($weekday_names[$day])) {
          continue;
        }
        $lazy_hours[$day] = $weekday_names[$day] . '<br />' . $i_item ?: $this->t('Closed');
      }
    }

    return $lazy_hours;
  }

  /**
   * Returns array with data for rendering Holidays Hours.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   *
   * @return array
   *   Holiday hours data array.
   */
  public function getBranchHolidayHours(NodeInterface $node) {
    $rows = [];
    $js_settings = [];
    $config = $this->configFactory->get('openy_field_holiday_hours.settings');

    // Calculate timezone offset.
    $tz = $this->configFactory->get('system.date')->get('timezone')['default'];
    $dt = new DrupalDateTime('now', $tz);
    $tz_offset = $dt->getOffset();

    // The Holiday Hours should be shown before N days.
    $show_before_offset = $config->get('show_before_offset') ?: self::SHOW_BEFORE_OFFSET;
    $show_before_offset = $tz_offset + $show_before_offset;

    // Also the Holiday Hours should be shown during N offset after a holiday.
    $show_after_offset = $config->get('show_after_offset') ?: self::SHOW_AFTER_OFFSET;
    $request_time = $this->time->getRequestTime();
    $show_all = FALSE;
    if ($node->hasField('field_show_all_holidays') && !$node->get('field_show_all_holidays')->isEmpty()) {
      $show_all = (bool) $node->get('field_show_all_holidays')->value;
    }
    if ($node->hasField('field_branch_holiday_hours')) {
      $holiday_hours = $node->field_branch_holiday_hours->getValue();
      foreach ($holiday_hours as $holiday) {
        // Skip holidays with empty date.
        if (empty($holiday['date'])) {
          continue;
        }
        $holiday_timestamp = $holiday['date'];
        if ($show_all || (
          $request_time < ($holiday_timestamp + $show_after_offset) &&
          ($holiday_timestamp - $request_time) <= $show_before_offset
        )) {
          $title = Html::escape($holiday['holiday']);
          $rows[] = [
            'data' => [
              $title,
              $holiday['hours'],
            ],
            'data-timestamp' => $holiday_timestamp,
          ];
          // Populate JS hours.
          $date = DrupalDateTime::createFromTimestamp($holiday_timestamp, $tz);
          $holiday_date = $date->format('Y-m-d');
          $js_settings[$holiday_date] = $title . '<br />' . $holiday['hours'];
        }
      }
      $table = [
        '#theme' => 'table',
        '#rows' => $rows,
        '#cache' => [
          'tags' => ['ymca_cron'],
        ],
      ];

    }
    return [
      'label' => $this->t('Holiday Hours'),
      'table' => $table ?? [],
      'js_settings' => $js_settings,
    ];
  }

  /**
   * Returns array with data for rendering Holidays Hours in Lazy Builder.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   *
   * @return array
   *   Holiday hours data array.
   */
  public function getLazyBranchHolidayHours(NodeInterface $node) {
    $js_settings = [];
    $config = $this->configFactory->get('openy_field_holiday_hours.settings');

    // Calculate timezone offset.
    $tz = $this->configFactory->get('system.date')->get('timezone')['default'];
    $dt = new DrupalDateTime('now', $tz);
    $tz_offset = $dt->getOffset();

    // The Holiday Hours should be shown before N days.
    $show_before_offset = $config->get('show_before_offset') ?: self::SHOW_BEFORE_OFFSET;
    $show_before_offset = $tz_offset + $show_before_offset;

    // Also the Holiday Hours should be shown during N offset after a holiday.
    $show_after_offset = $config->get('show_after_offset') ?: self::SHOW_AFTER_OFFSET;
    $request_time = $this->time->getRequestTime();
    $show_all = FALSE;
    if ($node->hasField('field_show_all_holidays') && !$node->get('field_show_all_holidays')->isEmpty()) {
      $show_all = (bool) $node->get('field_show_all_holidays')->value;
    }
    if ($node->hasField('field_branch_holiday_hours')) {
      $holiday_hours = $node->field_branch_holiday_hours->getValue();
      foreach ($holiday_hours as $holiday) {
        // Skip holidays with empty date.
        if (empty($holiday['date'])) {
          continue;
        }
        $holiday_timestamp = $holiday['date'];
        if ($show_all || (
          $request_time < ($holiday_timestamp + $show_after_offset) &&
          ($holiday_timestamp - $request_time) <= $show_before_offset
        )) {
          $title = Html::escape($holiday['holiday']);
          $date = DrupalDateTime::createFromTimestamp($holiday_timestamp, $tz);
          $holiday_date = $date->format('Y-m-d');
          $js_settings[$holiday_date] = $title . '<br />' . $holiday['hours'];
        }
      }

    }
    return $js_settings;
  }

  /**
   * Get array of weekday names.
   *
   * @return string[]
   *   Weekday names.
   */
  public function getWeekdayNames(): array {
    return [
      'Mon' => $this->t('Monday'),
      'Tue' => $this->t('Tuesday'),
      'Wed' => $this->t('Wednesday'),
      'Thu' => $this->t('Thursday'),
      'Fri' => $this->t('Friday'),
      'Sat' => $this->t('Saturday'),
      'Sun' => $this->t('Sunday'),
    ];
  }

  /**
   * Get timezone.
   *
   * @return string
   *   Timezone.
   */
  public function getTimezone() {
    return $this->configFactory->get('system.date')->get('timezone')['default'];
  }

}
