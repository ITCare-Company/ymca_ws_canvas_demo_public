<?php

/**
 * @file
 * Post update functions for the action useless machine.
 */

/**
 * Uninstall me.
 */
function action_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['action']);
}
