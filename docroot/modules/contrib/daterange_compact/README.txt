INTRODUCTION
------------

The compact date/time range formatter module provides the ability to display
date/time range fields in a more compact form, by omitting the month or year
where it would be duplicated.

Examples:
 - 24–25 January 2017 (start & end dates are within the same month)
 - 29 January–3 February 2017 (start & end dates are within the same year)
 - 9:00am–4:30pm, 1 April 2017 (start & end times are both on the same day)

For a full description of the module, visit the project page:
https://drupal.org/project/daterange_compact

To submit bug reports and feature suggestions, or to track changes:
https://drupal.org/project/issues/daterange_compact


REQUIREMENTS
------------

This module requires the 'Datetime range' core module.


INSTALLATION
------------

Install as you would normally install a contributed Drupal module. See:
https://www.drupal.org/docs/8/extending-drupal-8/installing-modules
for further information.


CONFIGURATION
-------------

Date/time range formats are configurable in Administration » Regional and
language » Date and time range formats.

For each format, the following options are available:

  - A default pattern, used used when the date/datetime range should be
    shown in full. This pattern is used if the start and end of the range
    are the same, or the range cannot be shown in a more compact form.
    This pattern is required.

  - Optional patterns for use when the start and end values occur during
    the same month. You can use these patterns to avoid displaying the
    same month name twice.

  - Optional patterns for use when the start and end values occur during
    the same year. You can use these patterns to avoid displaying the
    same year number twice.

  - Optional patterns for use when the start and end values occur during
    the same day. You can use these patterns to avoid displaying the date
    twice.

  - Separator strings that are placed in between the start and end values.

All patterns follow the same conventions as Drupal core's date formats,
and use the PHP date/time formatting tokens as described at:
http://www.php.net/manual/en/function.date.php


MAINTAINERS
-----------

Current maintainers:
 * Erik Erskine (erik.erskine) - https://drupal.org/u/erikerskine
