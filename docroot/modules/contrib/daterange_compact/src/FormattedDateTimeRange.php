<?php

declare(strict_types=1);

namespace Drupal\daterange_compact;

/**
 * Holds a formatted date range.
 */
class FormattedDateTimeRange {

  public string $text;

  /**
   * Constructor.
   *
   * @internal
   *
   * @param string $text
   */
  function __construct(string $text) {
    $this->text = $text;
  }

  /**
   * The text representation of this range.
   *
   * Since the formats may contain user input, this value should be escaped
   * when output.
   */
  function __toString(): string {
    return $this->text;
  }

}
