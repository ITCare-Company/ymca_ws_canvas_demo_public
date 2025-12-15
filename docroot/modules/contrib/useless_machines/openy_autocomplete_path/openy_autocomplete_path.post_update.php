<?php

/**
 * @file
 * Post update functions for the openy_autocomplete_path useless machine.
 */

/**
 * Uninstall me.
 */
function openy_autocomplete_path_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['openy_autocomplete_path']);
}
