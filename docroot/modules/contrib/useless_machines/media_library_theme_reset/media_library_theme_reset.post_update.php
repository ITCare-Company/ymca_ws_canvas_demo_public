<?php

/**
 * @file
 * Post update functions for the media_library_theme_reset useless machine.
 */

/**
 * Uninstall me.
 */
function media_library_theme_reset_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['media_library_theme_reset']);
}
