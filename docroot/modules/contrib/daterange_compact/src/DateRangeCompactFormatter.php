<?php

declare(strict_types=1);

namespace Drupal\daterange_compact;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface;

/**
 * Provides a service to handle compact formatting of date ranges.
 */
class DateRangeCompactFormatter implements DateRangeCompactFormatterInterface {

  /**
   * The config entity storage for compact date range formats.
   */
  protected EntityStorageInterface $formatStorage;

  /**
   * The core date formatter.
   */
  protected DateFormatterInterface $coreDateFormatter;

  /**
   * Constructs the compact date range formatter service.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The core date formatter.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, DateFormatterInterface $date_formatter) {
    $this->formatStorage = $entity_type_manager->getStorage('daterange_compact_format');
    $this->coreDateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public function formatDateRange(string $startDate, string $endDate, string $type = 'medium_date', $timezone = NULL, $langcode = NULL): FormattedDateTimeRange {
    $startTimestamp = (new DrupalDateTime($startDate, $timezone))->getTimestamp();
    $endTimestamp = (new DrupalDateTime($endDate, $timezone))->getTimestamp();
    return $this->formatTimestampRange($startTimestamp, $endTimestamp, $type, $timezone, $langcode);
  }

  /**
   * {@inheritdoc}
   */
  public function formatTimestampRange(int $start_timestamp, int $end_timestamp, string $type = 'medium_datetime', $timezone = NULL, $langcode = NULL): FormattedDateTimeRange {
    /** @var \Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface $format */
    $format = $this->formatStorage->load($type);

    $patterns = $format
      ? $this->derivePatterns($format, $start_timestamp, $end_timestamp, $timezone)
      : $this->fallbackPatterns();

    $start_pattern = $patterns->startPattern;
    $end_pattern = $patterns->endPattern;
    $separator = $patterns->separator;

    // Delegate the actual output to core's date formatting service.
    $start_text = $start_pattern ? $this->coreDateFormatter->format($start_timestamp, 'custom', $start_pattern, $timezone, $langcode) : '';
    $end_text = $end_pattern ? $this->coreDateFormatter->format($end_timestamp, 'custom', $end_pattern, $timezone, $langcode) : '';
    return ($start_text == $end_text)
      ? new FormattedDateTimeRange($start_text)
      : new FormattedDateTimeRange($start_text . $separator . $end_text);
  }

  /**
   * Derive patterns for the start and end dates according to their values.
   *
   * Examines the values of start and end in the context of the given timezone
   * and picks suitable patterns, falling back to default ones if necessary.
   *
   * @param \Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface $format
   *   The compact date range format configuration entity.
   * @param int $start_timestamp
   *   A UNIX timestamp representing the start time.
   * @param int $end_timestamp
   *   A UNIX timestamp representing the end time.
   * @param string|null $timezone
   *   (optional) Time zone identifier, as described at
   *   http://php.net/manual/timezones.php Defaults to the time zone used to
   *   display the page.
   */
  private function derivePatterns(DateRangeCompactFormatInterface $format, $start_timestamp, $end_timestamp, $timezone): Patterns {
    $patterns = NULL;

    if ($start_timestamp === $end_timestamp) {
      $patterns = $format->getDefaultPatterns();
      $patterns->endPattern = '';
      $patterns->separator = '';
    }

    if (!$patterns && self::isSameDay($start_timestamp, $end_timestamp, $timezone)) {
      $patterns = $format->getSameDayPatterns();
      if ($format->same_day_omit_duplicate_ampm && self::isSameHalfDay($start_timestamp, $end_timestamp, $timezone)) {
        Modifier::applyRemoveDuplicateAmPm($start_timestamp, $end_timestamp, $patterns, $timezone);
      }
    }

    if (!$patterns && self::isSameMonth($start_timestamp, $end_timestamp, $timezone)) {
      $patterns = $format->getSameMonthPatterns();
    }

    if (!$patterns && self::isSameYear($start_timestamp, $end_timestamp, $timezone)) {
      $patterns = $format->getSameYearPatterns();
    }

    if (!$patterns) {
      $patterns = $format->getDefaultPatterns();
    }

    // Convert times like "9:00am" into "9am" where possible.
    if ($format->zero_minutes_omit) {
      Modifier::removeZeroMinutes($start_timestamp, $end_timestamp, $patterns,  $format->zero_minutes_omit_pattern, $timezone);
    }

    return $patterns;
  }

  /**
   * Derive a set of patterns based on the 'fallback' core date format.
   *
   * These are used if the formatDateRange() or formatTimestampRange()
   * functions are passed the ID of a non-existent compact date range format.
   */
  private function fallbackPatterns(): Patterns {
    $core_fallback_date_format = DateFormat::load('fallback');
    assert($core_fallback_date_format, 'Compact date format was not found and there is no fallback code date format');
    return new Patterns(
      $core_fallback_date_format->getPattern(),
      $core_fallback_date_format->getPattern(),
      ' - ');
  }

  private static function isSameHalfDay($start_timestamp, $end_timestamp, $timezone): bool {
    return DrupalDateTime::createFromTimestamp($start_timestamp, $timezone)->format('Ymda') ===
      DrupalDateTime::createFromTimestamp($end_timestamp, $timezone)->format('Ymda');
  }

  private static function isSameDay($start_timestamp, $end_timestamp, $timezone): bool {
    return DrupalDateTime::createFromTimestamp($start_timestamp, $timezone)->format('Ymd') ===
      DrupalDateTime::createFromTimestamp($end_timestamp, $timezone)->format('Ymd');
  }

  private static function isSameMonth($start_timestamp, $end_timestamp, $timezone): bool {
    return DrupalDateTime::createFromTimestamp($start_timestamp, $timezone)->format('Ym') ===
      DrupalDateTime::createFromTimestamp($end_timestamp, $timezone)->format('Ym');
  }

  private static function isSameYear($start_timestamp, $end_timestamp, $timezone): bool {
    return DrupalDateTime::createFromTimestamp($start_timestamp, $timezone)->format('Y') ===
      DrupalDateTime::createFromTimestamp($end_timestamp, $timezone)->format('Y');
  }

}
