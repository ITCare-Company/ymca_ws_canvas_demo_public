<?php

declare(strict_types=1);

namespace Drupal\daterange_compact;

/**
 * Provides an interface defining a compact date range formatter.
 */
interface DateRangeCompactFormatterInterface {

  /**
   * Formats a date range, using a compact date range format.
   *
   * @param string $startDate
   *   An ISO-8601 format string representing the start date.
   * @param string $endDate
   *    ISO-8601 format string representing the end date.
   * @param string $type
   *   (optional) The machine name of an administrator-defined compact
   *   date range format. Defaults to the pre-installed 'medium_date' format.
   * @param string|null $timezone
   *   (optional) Time zone identifier, as described at
   *   http://php.net/manual/timezones.php Defaults to the time zone used to
   *   display the page.
   * @param string|null $langcode
   *   (optional) Language code to translate to. NULL (default) means to use
   *   the user interface language for the page.
   *
   * @return \Drupal\daterange_compact\FormattedDateTimeRange
   *   A translated date range value in the requested format.
   */
  public function formatDateRange(string $startDate, string $endDate, string $type = 'medium_date', $timezone = NULL, $langcode = NULL): FormattedDateTimeRange;

  /**
   * Formats a date and time range, using a datetime range format type.
   *
   * @param int $start_timestamp
   *   A UNIX timestamp representing the start time.
   * @param int $end_timestamp
   *   A UNIX timestamp representing the end time.
   * @param string $type
   *   (optional) The machine name of an administrator-defined compact
   *   date range format. Defaults to the pre-installed 'medium_datetime'
   *   format.
   * @param string|null $timezone
   *   (optional) Time zone identifier, as described at
   *   http://php.net/manual/timezones.php Defaults to the time zone used to
   *   display the page.
   * @param string|null $langcode
   *   (optional) Language code to translate to. NULL (default) means to use
   *   the user interface language for the page.
   *
   * @return \Drupal\daterange_compact\FormattedDateTimeRange
   *   A translated timestamp range value in the requested format.
   */
  public function formatTimestampRange(int $start_timestamp, int $end_timestamp, string $type = 'medium_datetime', $timezone = NULL, $langcode = NULL): FormattedDateTimeRange;

}
