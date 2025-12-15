<?php

namespace Drupal\openy_home_branch\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Home branch settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['openy_home_branch.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'openy_home_branch_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('openy_home_branch.settings');

    $form['popup_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Popup title'),
      '#description' => $this->t('Title of the Home branch popup'),
      '#default_value' => $config->get('popup_title'),
      '#required' => TRUE,
    ];

    $form['popup_description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Popup description'),
      '#default_value' => $config->get('popup_description'),
      '#required' => TRUE,
    ];

    $form['popup_learn_more'] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Popup Learn more text'),
      '#description' => $this->t('The text is displayed when user click the Learn more link in the Home branch popup.'),
      '#default_value' => $config->get('popup_learn_more')['value'],
      '#required' => TRUE,
    ];

    $form['popup_delay'] = [
      '#type' => 'number',
      '#title' => $this->t('Popup delay'),
      '#description' => $this->t('Delay in seconds.'),
      '#default_value' => $config->get('popup_delay'),
      '#maxlength' => 32,
      '#min' => 0,
      '#number_type' => 'integer',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('openy_home_branch.settings');
    $config->set('popup_title', $form_state->getValue('popup_title'));
    $config->set('popup_description', $form_state->getValue('popup_description'));
    $config->set('popup_learn_more', $form_state->getValue('popup_learn_more'));
    $config->set('popup_delay', $form_state->getValue('popup_delay'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
