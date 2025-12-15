<?php

declare(strict_types=1);

namespace Drupal\Tests\daterange_compact\Kernel;

use Drupal\daterange_compact\Entity\DateRangeCompactFormat;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests compact date range formatter functionality.
 *
 * These tests cover the daterange_compact.daterange_compact.formatter service
 * only, not the field formatter (see DateRangeCompactFieldFormatterTest for
 * that).
 */
class DateRangeCompactFormatterTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'datetime',
    'datetime_range',
    'daterange_compact',
    'user',
  ];

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['system']);
    $this->installConfig(['daterange_compact']);

    // Create a typical date format for USA.
    DateRangeCompactFormat::create([
      'id' => 'usa_date',
      'label' => 'USA (date only)',
      'default_pattern' => 'F jS, Y',
      'default_separator' => ' - ',
      'same_month_start_pattern' => 'F jS',
      'same_month_end_pattern' => 'jS, Y',
      'same_year_start_pattern' => 'F jS',
      'same_year_end_pattern' => 'F jS, Y',
    ])->save();

    // Create a typical datetime format for USA.
    // This format also contains varying separators to address the use case
    // in #2959070.
    DateRangeCompactFormat::create([
      'id' => 'usa_datetime',
      'label' => 'USA (date & time)',
      'default_pattern' => 'g:ia \o\n F jS, Y',
      'default_separator' => ' - ',
      'same_day_start_pattern' => 'g:ia',
      'same_day_end_pattern' => 'g:ia \o\n F jS, Y',
      'same_day_separator' => '-',
    ])->save();

    // Create a ISO-8601 date format without any compact variations.
    DateRangeCompactFormat::create([
      'id' => 'iso_8601_date',
      'label' => 'ISO-8601 (date only)',
      'default_pattern' => 'Y-m-d',
      'default_separator' => ' - ',
    ])->save();

    // Create a ISO-8601 datetime format without any compact variations.
    DateRangeCompactFormat::create([
      'id' => 'iso_8601_datetime',
      'label' => 'ISO-8601 (date & time)',
      'default_pattern' => 'Y-m-d\TH:i:s',
      'default_separator' => ' - ',
    ])->save();

    // Create a "year only" format to addresses the use case in #2890621,
    // where smaller units of time are omitted from the output.
    DateRangeCompactFormat::create([
      'id' => 'year_only',
      'label' => 'Year only',
      'default_pattern' => 'Y',
      'default_separator' => '-',
    ])->save();

    // Create a "month & year only" format to addresses the use case in
    // #2890621, where smaller units of time are omitted from the output.
    DateRangeCompactFormat::create([
      'id' => 'month_and_year_only',
      'label' => 'Month & year only',
      'default_pattern' => 'F Y',
      'default_separator' => '-',
      'same_month_start_pattern' => '',
      'same_month_end_pattern' => 'F Y',
      'same_year_start_pattern' => 'F',
      'same_year_end_pattern' => 'F Y',
    ])->save();

    // Formats for testing features below.
    $features_base_config = [
      'default_pattern' => 'j F Y g:ia',
      'default_separator' => ' - ',
      'same_day_start_pattern' => 'j F Y g:ia',
      'same_day_end_pattern' => 'g:ia',
      'same_day_separator' => '-',
      'same_day_omit_duplicate_ampm' => FALSE,
      'zero_minutes_omit' => FALSE,
      'zero_minutes_omit_pattern' => ':i',
    ];

    DateRangeCompactFormat::create([
        'id' => 'features_none',
        'label' => 'Features (none)',
      ] + $features_base_config)->save();

    DateRangeCompactFormat::create([
        'id' => 'features_ampm',
        'label' => 'Features (omit duplicate am/pm)',
        'same_day_omit_duplicate_ampm' => TRUE,
      ] + $features_base_config)->save();

    DateRangeCompactFormat::create([
        'id' => 'features_zeros',
        'label' => 'Features (omit zero minutes)',
        'zero_minutes_omit' => TRUE,
      ] + $features_base_config)->save();

    DateRangeCompactFormat::create([
        'id' => 'features_all',
        'label' => 'Features (all)',
        'same_day_omit_duplicate_ampm' => TRUE,
        'zero_minutes_omit' => TRUE,
      ] + $features_base_config)->save();
  }

  /**
   * Tests the display of date-only range fields.
   */
  public function testDateRanges() {
    $all_data = [];

    // Same day.
    $all_data[] = [
      'start' => '2017-01-01',
      'end' => '2017-01-01',
      'expected' => [
        'medium_date' => '1 January 2017',
        'usa_date' => 'January 1st, 2017',
        'iso_8601_date' => '2017-01-01',
        'year_only' => '2017',
        'month_and_year_only' => 'January 2017',
      ],
    ];

    // Different days, same month.
    $all_data[] = [
      'start' => '2017-01-02',
      'end' => '2017-01-03',
      'expected' => [
        'medium_date' => '2–3 January 2017',
        'usa_date' => 'January 2nd - 3rd, 2017',
        'iso_8601_date' => '2017-01-02 - 2017-01-03',
        'year_only' => '2017',
        'month_and_year_only' => 'January 2017',
      ],
    ];

    // Different months, same year.
    $all_data[] = [
      'start' => '2017-01-04',
      'end' => '2017-02-05',
      'expected' => [
        'medium_date' => '4 January – 5 February 2017',
        'usa_date' => 'January 4th - February 5th, 2017',
        'iso_8601_date' => '2017-01-04 - 2017-02-05',
        'year_only' => '2017',
        'month_and_year_only' => 'January-February 2017',
      ],
    ];

    // Different years.
    $all_data[] = [
      'start' => '2017-01-06',
      'end' => '2018-02-07',
      'expected' => [
        'medium_date' => '6 January 2017 – 7 February 2018',
        'usa_date' => 'January 6th, 2017 - February 7th, 2018',
        'iso_8601_date' => '2017-01-06 - 2018-02-07',
        'year_only' => '2017-2018',
        'month_and_year_only' => 'January 2017-February 2018',
      ],
    ];

    /** @var \Drupal\daterange_compact\DateRangeCompactFormatterInterface $formatter */
    $formatter = $this->container->get('daterange_compact.formatter');

    foreach ($all_data as $data) {
      foreach ($data['expected'] as $format => $expected) {
        $actual = $formatter->formatDateRange($data['start'], $data['end'], $format);
        $message = "Using the $format format for " . $data['start'] . ' to ' . $data['end'];
        $this->assertEquals($expected, $actual, $message);
      }
    }
  }

  /**
   * Tests the display of date and time range fields.
   *
   * Note: the default timezone for unit tests is Australia/Sydney
   * see https://www.drupal.org/node/2498619 for why
   * Australia/Sydney is UTC +10:00 (normal) or UTC +11:00 (DST)
   * DST starts first Sunday in October
   * DST ends first Sunday in April.
   */
  public function testDateTimeRanges() {
    $all_data = [];

    // Same day.
    $all_data[] = [
      'start' => '2017-01-01T20:00:00',
      'end' => '2017-01-01T23:00:00',
      'expected' => [
        'medium_datetime' => '1 January 2017 20:00–23:00',
        'usa_datetime' => '8:00pm-11:00pm on January 1st, 2017',
        'iso_8601_datetime' => '2017-01-01T20:00:00 - 2017-01-01T23:00:00',
        'year_only' => '2017',
        'month_and_year_only' => 'January 2017',
      ],
    ];

    // Different day in UTC, same day in Australia.
    $all_data[] = [
      'start' => '2017-01-02T10:00:00',
      'end' => '2017-01-02T12:00:00',
      'expected' => [
        'medium_datetime' => '2 January 2017 10:00–12:00',
        'usa_datetime' => '10:00am-12:00pm on January 2nd, 2017',
        'iso_8601_datetime' => '2017-01-02T10:00:00 - 2017-01-02T12:00:00',
        'year_only' => '2017',
        'month_and_year_only' => 'January 2017',
      ],
    ];

    // Same day in UTC, different day in Australia.
    $all_data[] = [
      'start' => '2017-01-01T23:00:00',
      'end' => '2017-01-02T02:00:00',
      'expected' => [
        'medium_datetime' => '1 January 2017 23:00 – 2 January 2017 02:00',
        'usa_datetime' => '11:00pm on January 1st, 2017 - 2:00am on January 2nd, 2017',
        'iso_8601_datetime' => '2017-01-01T23:00:00 - 2017-01-02T02:00:00',
        'year_only' => '2017',
        'month_and_year_only' => 'January 2017',
      ],
    ];

    // Different days in UTC and Australia, also spans DST change.
    $all_data[] = [
      'start' => '2017-04-01T12:00:00',
      'end' => '2017-04-08T11:00:00',
      'expected' => [
        'medium_datetime' => '1 April 2017 12:00 – 8 April 2017 11:00',
        'usa_datetime' => '12:00pm on April 1st, 2017 - 11:00am on April 8th, 2017',
        'iso_8601_datetime' => '2017-04-01T12:00:00 - 2017-04-08T11:00:00',
        'year_only' => '2017',
        'month_and_year_only' => 'April 2017',
      ],
    ];

    // Tests for omitting zeros and duplicate am/pm handling.
    $all_data[] = [
      'start' => '2024-03-30T09:00:00',
      'end' => '2024-03-30T09:30:00',
      'expected' => [
        'features_none' => '30 March 2024 9:00am-9:30am',
        'features_ampm' => '30 March 2024 9:00-9:30am',
        'features_zeros' => '30 March 2024 9am-9:30am',
        'features_all' => '30 March 2024 9-9:30am',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:15:00',
      'end' => '2024-03-30T09:45:00',
      'expected' => [
        'features_none' => '30 March 2024 9:15am-9:45am',
        'features_ampm' => '30 March 2024 9:15-9:45am',
        'features_zeros' => '30 March 2024 9:15am-9:45am',
        'features_all' => '30 March 2024 9:15-9:45am',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:30:00',
      'end' => '2024-03-30T10:00:00',
      'expected' => [
        'features_none' => '30 March 2024 9:30am-10:00am',
        'features_ampm' => '30 March 2024 9:30-10:00am',
        'features_zeros' => '30 March 2024 9:30am-10am',
        'features_all' => '30 March 2024 9:30-10am',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:00:00',
      'end' => '2024-03-30T13:30:00',
      'expected' => [
        'features_none' => '30 March 2024 9:00am-1:30pm',
        'features_ampm' => '30 March 2024 9:00am-1:30pm',
        'features_zeros' => '30 March 2024 9am-1:30pm',
        'features_all' => '30 March 2024 9am-1:30pm',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:15:00',
      'end' => '2024-03-30T13:45:00',
      'expected' => [
        'features_none' => '30 March 2024 9:15am-1:45pm',
        'features_ampm' => '30 March 2024 9:15am-1:45pm',
        'features_zeros' => '30 March 2024 9:15am-1:45pm',
        'features_all' => '30 March 2024 9:15am-1:45pm',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:30:00',
      'end' => '2024-03-30T14:00:00',
      'expected' => [
        'features_none' => '30 March 2024 9:30am-2:00pm',
        'features_ampm' => '30 March 2024 9:30am-2:00pm',
        'features_zeros' => '30 March 2024 9:30am-2pm',
        'features_all' => '30 March 2024 9:30am-2pm',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:00:00',
      'end' => '2024-03-31T09:30:00',
      'expected' => [
        'features_none' => '30 March 2024 9:00am - 31 March 2024 9:30am',
        'features_ampm' => '30 March 2024 9:00am - 31 March 2024 9:30am',
        'features_zeros' => '30 March 2024 9am - 31 March 2024 9:30am',
        'features_all' => '30 March 2024 9am - 31 March 2024 9:30am',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:15:00',
      'end' => '2024-03-31T09:45:00',
      'expected' => [
        'features_none' => '30 March 2024 9:15am - 31 March 2024 9:45am',
        'features_ampm' => '30 March 2024 9:15am - 31 March 2024 9:45am',
        'features_zeros' => '30 March 2024 9:15am - 31 March 2024 9:45am',
        'features_all' => '30 March 2024 9:15am - 31 March 2024 9:45am',
      ]
    ];
    $all_data[] = [
      'start' => '2024-03-30T09:30:00',
      'end' => '2024-03-31T10:00:00',
      'expected' => [
        'features_none' => '30 March 2024 9:30am - 31 March 2024 10:00am',
        'features_ampm' => '30 March 2024 9:30am - 31 March 2024 10:00am',
        'features_zeros' => '30 March 2024 9:30am - 31 March 2024 10am',
        'features_all' => '30 March 2024 9:30am - 31 March 2024 10am',
      ]
    ];

    /** @var \Drupal\daterange_compact\DateRangeCompactFormatterInterface $formatter */
    $formatter = $this->container->get('daterange_compact.formatter');

    foreach ($all_data as $data) {
      foreach ($data['expected'] as $format => $expected) {
        $start = \DateTime::createFromFormat('Y-m-d\TH:i:s', $data['start'])->getTimestamp();
        $end = \DateTime::createFromFormat('Y-m-d\TH:i:s', $data['end'])->getTimestamp();

        $actual = $formatter->formatTimestampRange($start, $end, $format);
        $message = "Using the $format format for " . $data['start'] . ' to ' . $data['end'];
        $this->assertEquals($expected, $actual, $message);
      }
    }
  }

  /**
   * Test the core 'fallback' date format.
   *
   * This should be used if the formatter is passed an ID of a
   * non-existent config entity.
   * https://www.drupal.org/project/daterange_compact/issues/3248081
   */
  public function testFallbackFormat() {
    /** @var \Drupal\daterange_compact\DateRangeCompactFormatterInterface $formatter */
    $formatter = $this->container->get('daterange_compact.formatter');

    $actual = $formatter->formatTimestampRange(0, 3600, 'dummy');
    $expected = 'Thu, 01/01/1970 - 10:00 - Thu, 01/01/1970 - 11:00';
    $this->assertEquals($expected, $actual, 'Use core fallback format for missing config entity');

    $actual = $formatter->formatDateRange('1970-01-01', '1970-01-02', 'dummy');
    $expected = 'Thu, 01/01/1970 - 00:00 - Fri, 01/02/1970 - 00:00';
    $this->assertEquals($expected, $actual, 'Use core fallback format for missing config entity');
  }

}
