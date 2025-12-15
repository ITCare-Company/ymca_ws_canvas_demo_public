<?php

/**
 * @file
 * Post update functions for the ckeditor_font useless machine.
 */

/**
 * Uninstall me.
 */
function inline_entity_menu_form_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['inline_entity_menu_form']);
}
