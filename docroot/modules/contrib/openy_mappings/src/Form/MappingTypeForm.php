<?php

namespace Drupal\openy_mappings\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the mapping type form.
 *
 * @package Drupal\openy_mappings\Form
 */
class MappingTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $mapping_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $mapping_type->label(),
      '#description' => $this->t("Label for the Mapping type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $mapping_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\openy_mappings\Entity\MappingType::load',
      ],
      '#disabled' => !$mapping_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $mapping_type = $this->entity;
    $status = $mapping_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Mapping type.', [
          '%label' => $mapping_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Mapping type.', [
          '%label' => $mapping_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($mapping_type->toUrl('collection'));
  }

}
