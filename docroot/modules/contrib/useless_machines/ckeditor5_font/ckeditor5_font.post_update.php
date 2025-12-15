<?php

/**
 * @file
 * Post update functions for the ckeditor_font useless machine.
 */

/**
 * Uninstall me.
 */
function ckeditor5_font_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['ckeditor5_font']);
}
