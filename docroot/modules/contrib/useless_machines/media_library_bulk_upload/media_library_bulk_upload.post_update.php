<?php

/**
 * @file
 * Post update functions for the media_library_bulk_upload useless machine.
 */

/**
 * Uninstall me.
 */
function media_library_bulk_upload_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['media_library_bulk_upload']);
}
