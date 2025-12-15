<?php

/**
 * @file
 * Post update functions for the color useless machine.
 */

/**
 * Uninstall me.
 */
function color_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['color']);
}
