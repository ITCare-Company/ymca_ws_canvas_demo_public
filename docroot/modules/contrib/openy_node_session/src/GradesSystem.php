<?php

namespace Drupal\openy_node_session;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class GradesSystem.
 */
class GradesSystem implements GradesSystemInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getGrades(): array {
    return [
      'PRE_K' => $this->t('Pre K'),
      'K' => $this->t('Kindergarten'),
      '1' => $this->t('Grade 1'),
      '2' => $this->t('Grade 2'),
      '3' => $this->t('Grade 3'),
      '4' => $this->t('Grade 4'),
      '5' => $this->t('Grade 5'),
      '6' => $this->t('Grade 6'),
      '7' => $this->t('Grade 7'),
      '8' => $this->t('Grade 8'),
      '9' => $this->t('Grade 9'),
      '10' => $this->t('Grade 10'),
      '11' => $this->t('Grade 11'),
      '12' => $this->t('Grade 12'),
    ];
  }

}
