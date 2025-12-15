<?php

/**
 * @file
 * Post update functions for the quickedit useless machine.
 */

/**
 * Uninstall me.
 */
function quickedit_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['quickedit']);
}
