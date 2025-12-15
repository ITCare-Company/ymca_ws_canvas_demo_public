<?php

use Drupal\Core\Extension\ModuleInstallerInterface;

/**
 * @file
 * Post update hooks for Y Layout Builder - Branch Hours.
 */

/**
 * Enable the 'y_branch' module.
 */
function lb_branch_hours_blocks_post_update_1_enable_y_branch() {
  $moduleInstaller = \Drupal::service('module_installer');
  assert($moduleInstaller instanceof ModuleInstallerInterface);
  $moduleInstaller->install(['y_branch']);
}
