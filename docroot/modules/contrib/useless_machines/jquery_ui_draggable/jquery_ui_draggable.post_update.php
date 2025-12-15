<?php

/**
 * @file
 * Post update functions for the jquery_ui_draggable useless machine.
 */

/**
 * Uninstall me.
 */
function jquery_ui_draggable_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['jquery_ui_draggable']);
}
