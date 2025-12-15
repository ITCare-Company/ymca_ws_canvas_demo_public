<?php

declare(strict_types=1);

namespace Drupal\daterange_compact;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * @internal
 */
final class Modifier {

  /**
   * Modification: turn 9:00am into 9am.
   */
  static function removeZeroMinutes(int $start_timestamp, int $end_timestamp, Patterns $patterns, string $zero_minutes_omit_pattern, $timezone): void {
    if (self::isZeroMinutes($start_timestamp, $timezone)) {
      $patterns->startPattern = str_replace($zero_minutes_omit_pattern, '', $patterns->startPattern);
    }
    if (self::isZeroMinutes($end_timestamp, $timezone)) {
      $patterns->endPattern = str_replace($zero_minutes_omit_pattern, '', $patterns->endPattern);
    }
  }

  private static function isZeroMinutes(int $timestamp, $timezone): bool {
    $datetime = DrupalDateTime::createFromTimestamp($timestamp, $timezone);
    return $datetime->format('i') === '00';
  }

  /**
   * Modification: turn 9am-10am into 9-10am
   */
  static function applyRemoveDuplicateAmPm(int $start_timestamp, int $end_timestamp, Patterns $patterns, $timezone): void {
    $start_value = DrupalDateTime::createFromTimestamp($start_timestamp, $timezone)->format('Ymd a');
    $end_value = DrupalDateTime::createFromTimestamp($end_timestamp, $timezone)->format('Ymd a');
    if ($start_value === $end_value) {
      // The regex here uses a negative look behind to match instances
      // of the letter 'a' that do not immediately follow a backslash.
      // Date formats like 'Y-m-d \a\t g:ia' are catered for.
      $patterns->startPattern = preg_replace('/(?<!\\\)a/', '', $patterns->startPattern);
    }
  }

}
