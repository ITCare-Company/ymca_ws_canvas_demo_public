<?php

/**
 * @file
 * Post update functions for the google_optimize useless machine.
 */

/**
 * Uninstall me.
 */
function google_optimize_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['google_optimize']);
}
