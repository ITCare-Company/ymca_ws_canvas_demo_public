<?php

/**
 * @file
 * Post update functions for the ckeditor_bootstrap_buttons useless machine.
 */

/**
 * Uninstall me.
 */
function ckeditor_bootstrap_buttons_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['ckeditor_bootstrap_buttons']);
}
