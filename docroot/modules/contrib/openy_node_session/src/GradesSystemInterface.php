<?php

namespace Drupal\openy_node_session;

/**
 * Interface GradesSystemInterface.
 *
 * @package Drupal\openy_node_session
 */
interface GradesSystemInterface {

  /**
   * Get educational grades
   *
   * @return array
   *   List of the educational grades.
   */
  public function getGrades(): array;

}
