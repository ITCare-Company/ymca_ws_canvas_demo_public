<?php

/**
 * @file
 * Post update functions for the openy_inline_editing useless machine.
 */

/**
 * Uninstall me.
 */
function openy_inline_editing_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['openy_inline_editing']);
}
