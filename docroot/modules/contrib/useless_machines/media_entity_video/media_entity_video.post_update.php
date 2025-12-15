<?php

/**
 * @file
 * Post update functions for the media_entity_video useless machine.
 */

/**
 * Uninstall me.
 */
function media_entity_video_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['media_entity_video']);
}
