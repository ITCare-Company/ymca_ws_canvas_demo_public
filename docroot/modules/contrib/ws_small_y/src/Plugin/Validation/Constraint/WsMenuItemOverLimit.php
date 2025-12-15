<?php

namespace Drupal\ws_small_y\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Validates that the first level of the menu has a limited number of items.
 *
 * @Constraint(
 *   id = "WsMenuItemOverLimit",
 *   label = @Translation("A first level of the menu has too many items.", context = "Validation"),
 *   type = "string"
 * )
 */
class WsMenuItemOverLimit extends Constraint {

  /**
   * The error message to be returned if validation fails.
   */
  public string $message = 'A new link cannot be added because the first level\'s menu item limit (%limit items) has been reached.';

}
