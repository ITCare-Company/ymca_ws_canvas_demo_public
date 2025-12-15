<?php

/**
 * @file
 * Post update functions for the colorbutton useless machine.
 */

/**
 * Uninstall me.
 */
function colorbutton_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['colorbutton']);
}
