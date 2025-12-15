<?php

namespace Drupal\y_donate\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with an embedded donation form.
 *
 * @Block(
 *   id = "y_donate_donate_form_block",
 *   admin_label = @Translation("Donation Form Embed Block"),
 *   category = @Translation("Common blocks")
 * )
 */
class DonateFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    if ($this->inPreview) {
      $preview = $this->t('This is a placeholder for the %provider form with ID %id.',
        [
          '%provider' => $config['form_provider'] ?? '',
          '%id' => $config['form_id'] ?? '',
        ]
      );
    }

    return [
      '#theme' => 'y_donate_form',
      '#preview' => $preview ?? NULL,
      '#donate_form_provider' => $config['form_provider'] ?? '',
      '#donate_form_id' => $config['form_id'] ?? '',
      '#amount' => \Drupal::request()->query->get('amount'),
      '#cache' => ['contexts' => ['url.path', 'url.query_args']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['form_provider'] = [
      '#type' => 'select',
      '#title' => $this->t('Donate Form Provider'),
      '#options' => $this->listFormProviders(),
      '#default_value' => $config['form_provider'] ?? '',
    ];

    $form['form_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Donate Form ID'),
      '#description' => $this->t('The Form ID from the donation provider. Use either a 4-digit ID for Luminate or a 36-character ID for Online Express.'),
      '#default_value' => $config['form_id'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['form_provider'] = $values['form_provider'];
    $this->configuration['form_id'] = $values['form_id'];
  }

  /**
   * Lists Form Providers for Donation form.
   *
   * To be overridden if a custom provider is needed. Array key should be the
   * same name as a twig file that will be used to render the form.
   *
   * @return string[]
   *   Array of form providers. Each key corresponds to a twig file name.
   */
  public function listFormProviders(): array {
    return [
      'luminate' => $this->t('Convio Luminate'),
      'online-express' => $this->t('Blackbaud Online Express'),
    ];
  }

}
