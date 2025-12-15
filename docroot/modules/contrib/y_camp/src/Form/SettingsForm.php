<?php

namespace Drupal\y_camp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings Form for Camps.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'y_camp_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'y_camp.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('y_camp.settings');

    $form['camp_backlink'] = [
      '#type' => 'details',
      '#title' => $this->t('Camps back link'),
      '#open' => TRUE,
    ];

    $form['camp_backlink']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link title'),
      '#default_value' => $config->get('link.title'),
      '#description' => $this->t('The title of the Camp back link. By default uses the Site name.'),
    ];

    $form['camp_backlink']['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link url'),
      '#default_value' => $config->get('link.url'),
      '#description' => $this->t('The URL for the Camp back link. This URL should starts from "/".'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('y_camp.settings');
    $config->set('link.title', $form_state->getValue('title'))->save();
    $url = trim($form_state->getValue('url'));
    if (!str_starts_with($url, '/')) {
      $url =  '/' . $url;
    }
    $config->set('link.url', $url)->save();
    parent::submitForm($form, $form_state);
  }

}
