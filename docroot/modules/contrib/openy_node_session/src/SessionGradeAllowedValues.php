<?php

namespace Drupal\openy_node_session;

/**
 * Provides logic for listing session's grade types.
 */
final class SessionGradeAllowedValues {

  /**
   * Gets the list of all available session's grade types.
   *
   * @return array
   *   The labels, keyed by ID.
   */
  public static function allowedValues() : array {
    return \Drupal::service('openy_node_session.grades_system')->getGrades();
  }

}

