<?php

namespace Drupal\openy_search_api\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings Form for openy_search_api.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * Constructs a \Drupal\openy_search_api\Form\SettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cache_tags_invalidator
   *   The cache tag invalidator.
   */
  public function __construct(ConfigFactoryInterface $config_factory, CacheTagsInvalidatorInterface $cache_tags_invalidator) {
    $this->setConfigFactory($config_factory);
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'openy_search_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'openy_search_api.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('openy_search_api.settings');

    $form['search_page_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search page ID'),
      '#description' => $this->t('Find the node id of the search results page by editing the page and looking in the URL for <code>/node/{node_id}/edit</code>'),
      '#size' => 40,
      '#default_value' => !empty($config->get('search_page_id')) ? $config->get('search_page_id') : '',
      '#required' => TRUE,
    ];

    $form['search_query_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search query key'),
      '#description' => $this->t('The argument preceding the search string in the URL. For example, in <code>/search?q=swim</code>, the query key is <code>q</code>. Changing this also requires changing the View that is performing the search.'),
      '#size' => 40,
      '#default_value' => !empty($config->get('search_query_key')) ? $config->get('search_query_key') : '',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $search_page_id = $form_state->getValue('search_page_id');

    // Set an error if the page id is not a number.
    if (!is_numeric($search_page_id)) {
      $form_state
        ->setErrorByName('search_page_id', $this
          ->t('The Search page ID must be a number.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('openy_search_api.settings');
    $config->set('search_page_id', $values['search_page_id']);
    $config->set('search_query_key', $values['search_query_key']);
    $config->save();

    $this->cacheTagsInvalidator->invalidateTags($config->getCacheTags());

    parent::submitForm($form, $form_state);
  }

}
