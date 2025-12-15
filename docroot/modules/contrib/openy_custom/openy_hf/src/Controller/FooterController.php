<?php

namespace Drupal\openy_hf\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * An footer controller.
 */
class FooterController extends ControllerBase {

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
