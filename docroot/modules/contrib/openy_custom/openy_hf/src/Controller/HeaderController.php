<?php

namespace Drupal\openy_hf\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An header controller.
 */
class HeaderController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $build = [
      '#type' => 'markup',
    ];
    return $build;
  }

}
