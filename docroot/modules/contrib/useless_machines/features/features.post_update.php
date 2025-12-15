<?php

/**
 * @file
 * Post update functions for the features useless machine.
 */

/**
 * Uninstall me.
 */
function features_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['features']);
}
