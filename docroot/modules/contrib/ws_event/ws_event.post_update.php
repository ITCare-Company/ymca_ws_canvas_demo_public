<?php

/**
 * @file
 * Post-update hooks for ws_event.
 */

use Drush\Drush;

/**
 * Migrate from core date field to smart date field.
 */
function ws_event_post_update_recur_field_update(&$sandbox) {
  return Drush::processManager()->drush(
    Drush::service('site.alias.manager')->getSelf(),
    'smart_date:migrate',
    ['lb_event', 'field_event_dates_smart', 'field_event_dates'])
    ->run();
}
