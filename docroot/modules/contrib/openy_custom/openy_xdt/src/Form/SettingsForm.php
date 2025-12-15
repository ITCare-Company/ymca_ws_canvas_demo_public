<?php

namespace Drupal\openy_xdt\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure YMCA Website Services Cross-domain Tracking (XDT) settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openy_xdt_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['openy_xdt.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Both items are stored as arrays in config, so they need to be imploded
    // to be displayed in the textarea.
    $form['cookies'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Cookies'),
      '#default_value' => implode(PHP_EOL, $this->config('openy_xdt.settings')->get('cookies') ?? []),
      '#description' => $this->t('The cookies to transfer to the query string. One per line. If empty, no action will be taken.'),
    ];
    $form['domains'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Domains'),
      '#default_value' => implode(PHP_EOL, $this->config('openy_xdt.settings')->get('domains') ?? []),
      '#description' => $this->t('The destination domains that will have cookies added to the query string. One per line. If empty, no action will be taken.'),
      '#placeholder' => 'www.example.com',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // If domains are set, ensure they are valid.
    if (!empty($form_state->getValue('domains'))) {
      $domains = preg_split('/\R/', $form_state->getValue('domains'));
      foreach ($domains as $domain) {
        if (!empty($domain) && !filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
          $form_state->setErrorByName('domain', $this->t('%domain is not a valid domain. Remove any slashes and only enter the part of the url between <em>https://</em> and the next <em>/</em>.', ['%domain' => $domain]));
        }
      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('openy_xdt.settings')
      // Split values at line breaks and filter out any empty values.
      ->set('cookies', array_filter(preg_split('/\R/', $form_state->getValue('cookies'))))
      ->set('domains', array_filter(preg_split('/\R/', $form_state->getValue('domains'))))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
