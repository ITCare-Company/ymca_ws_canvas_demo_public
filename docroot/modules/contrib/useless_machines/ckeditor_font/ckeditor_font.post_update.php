<?php

/**
 * @file
 * Post update functions for the ckeditor_font useless machine.
 */

/**
 * Uninstall me.
 */
function ckeditor_font_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['ckeditor_font']);
}
