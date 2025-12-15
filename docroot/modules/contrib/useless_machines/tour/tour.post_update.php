<?php

/**
 * @file
 * Post update functions for the tour useless machine.
 */

/**
 * Uninstall me.
 */
function tour_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['tour']);
}
