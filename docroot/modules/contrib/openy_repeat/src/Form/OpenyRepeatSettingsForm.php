<?php

namespace Drupal\openy_repeat\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class OpenyRepeatSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['openy_repeat.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openy_repeat_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('openy_repeat.settings');

    $form['schedule_booking_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Schedule booking URL'),
      '#default_value' => $config->get('schedule_booking_url') ?? '',
      '#description' => $this->t('Enter the URL for the "My Bookings" button.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('openy_repeat.settings')
      ->set('schedule_booking_url', $form_state->getValue('schedule_booking_url'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
