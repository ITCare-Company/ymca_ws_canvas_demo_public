<?php

namespace Drupal\ws_promotion\Form;


use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Form\NodePreviewForm;

/**
 * Contains a form for switching the view mode of a node during preview.
 *
 * @internal
 */
class NodePromoPreviewForm extends NodePreviewForm {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Gets the form entity.
   *
   * The form entity which has been used for populating form element defaults.
   *
   * @return \Drupal\Core\Entity\EntityInterface|NULL
   *   The current form entity.
   */
  public function getEntity() {
    return $this->entity ?? NULL;
  }

  /**
   * Sets the form entity.
   *
   * Sets the form entity which will be used for populating form element
   * defaults. Usually, the form entity gets updated by
   * \Drupal\Core\Entity\EntityFormInterface::submit(), however this may
   * be used to completely exchange the form entity, e.g. when preparing the
   * rebuild of a multi-step form.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity the current form should operate upon.
   *
   * @return $this
   */
  public function setEntity(EntityInterface $entity) {
    $this->entity = $entity;
    return $this;
  }

}
