<?php

namespace Drupal\ws_small_y_statistics\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if the Small Y Statistics block has at least 2 items.
 *
 * @Constraint(
 *   id = "smallYminStatisticsItems",
 *   label = @Translation("A statistics block should have at least 2 items.", context = "Validation"),
 *   type = "string"
 * )
 */
class MinStatisticsItems extends Constraint {

  /**
   * The error message to be returned if validation fails.
   */
  public string $message = 'A statistics block should have at least %requirement items';

}
