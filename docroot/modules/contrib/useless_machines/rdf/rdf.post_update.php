<?php

/**
 * @file
 * Post update functions for the rdf useless machine.
 */

/**
 * Uninstall me.
 */
function rdf_post_update_uninstall() {
  \Drupal::service('module_installer')->uninstall(['rdf']);
}
