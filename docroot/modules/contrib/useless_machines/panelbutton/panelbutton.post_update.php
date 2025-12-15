<?php

/**
 * @file
 * Post update functions for the panelbutton useless machine.
 */

/**
 * Uninstall me.
 */
function panelbutton_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['panelbutton']);
}
