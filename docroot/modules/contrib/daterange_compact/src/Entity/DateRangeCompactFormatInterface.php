<?php

declare(strict_types=1);

namespace Drupal\daterange_compact\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\daterange_compact\Patterns;

/**
 * Provides an interface for defining a compact date range format.
 *
 * Configuration entities of this type contain the following properties:
 *     - "default_pattern" - the default format pattern, used for ranges
 *          where the start/end values are the same, ranges that span
 *          multiple years, or where no more specific pattern is available.
 *     - "default_separator" - the separator string to place in between the
 *          start and end values.
 *     - "same_day_start_pattern" - the pattern with which to format
 *          the start date & time, for ranges that are contained within
 *          a single day. Not used for date-only range types.
 *     - "same_day_end_pattern" - as above but for the end date & time.
 *     - "same_day_separator" - alternative separator for use with the
 *          above patterns.
 *     - "same_day_omit_duplicate_ampm" - boolean indicating whether a
 *          duplicate 'am' or 'pm' should be removed for ranges contained
 *          within a single morning or afternoon.
 *     - "same_month_start_pattern" - the pattern with which to format
 *          the start date, for ranges that span multiple days within
 *          the same calendar month.
 *     - "same_month_end_pattern" - as above, but for the end date.
 *     - "same_month_separator" - alternative separator for use with the
 *          above patterns.
 *     - "same_year_start_pattern" - the pattern with which to format
 *         the start date, for ranges that span multiple months within
 *         the same calendar year.
 *     - "same_year_end_pattern" - as above but for the end date.
 *     - "same_year_separator" - alternative separator for use with the
 *          above patterns.
 *     - "zero_minutes_omit" - boolean indicating whether the minutes value
 *          within a time should be omitted if zero, eg 9am vs 9:00am.
 *     - "zero_minutes_omit_pattern" - the part of the date format
 *          corresponding to minutes that will be omitted if applicable.
 */
interface DateRangeCompactFormatInterface extends ConfigEntityInterface {

  /**
   * Default patterns to use. Always available.
   */
  public function getDefaultPatterns(): Patterns;

//  /**
//   * Preferred patterns for same half day ranges, if available.
//   */
//  public function getSameHalfDayPatterns(): ?Patterns;

  /**
   * Preferred patterns for same day ranges, if available.
   */
  public function getSameDayPatterns(): ?Patterns;

  /**
   * Preferred patterns for same month ranges, if available.
   */
  public function getSameMonthPatterns(): ?Patterns;

  /**
   * Preferred patterns for same year ranges, if available.
   */
  public function getSameYearPatterns(): ?Patterns;

}
