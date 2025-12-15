<?php

/**
 * @file
 * Post update functions for the geysir useless machine.
 */

/**
 * Uninstall me.
 */
function geysir_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['geysir']);
}
