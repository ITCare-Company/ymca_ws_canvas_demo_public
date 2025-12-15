<?php

namespace Drupal\small_y_demo_content\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Fills in link options with icon information.
 *
 * @MigrateProcessPlugin(
 *   id = "small_y_menu_link_class"
 * )
 */
class SmallYMenuLinkClass extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$value) {
      return NULL;
    }
    $options = [
      'attributes' => [
        'class' => $value,
      ],
    ];
    return $options;
  }

}
