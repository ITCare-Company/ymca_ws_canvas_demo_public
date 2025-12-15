<?php

/**
 * @file
 * Post update functions for the bartik useless machine.
 */

/**
 * Uninstall me.
 */
function bartik_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['bartik']);
}
