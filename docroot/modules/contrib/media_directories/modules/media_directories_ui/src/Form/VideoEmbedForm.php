<?php

namespace Drupal\media_directories_ui\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Utility\Token;
use Drupal\video_embed_field\ProviderManagerInterface;
use Drupal\video_embed_media\Plugin\media\Source\VideoEmbedField;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A form to add remote content using Video Embed Field resources.
 */
class VideoEmbedForm extends AddMediaFormBase {

  /**
   * The embed provider plugin manager.
   *
   * @var \Drupal\video_embed_field\ProviderManagerInterface
   */
  protected $providerManager;

  /**
   * Constructs a new VideoEmbedForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\video_embed_field\ProviderManagerInterface $provider_manager
   *   Video embed field provider manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user, Token $token, ThemeManagerInterface $theme_manager, RendererInterface $renderer, ProviderManagerInterface $provider_manager = NULL) {
    parent::__construct($entity_type_manager, $current_user, $token, $theme_manager, $renderer);
    $this->providerManager = $provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('token'),
      $container->get('theme.manager'),
      $container->get('renderer'),
      $container->get('video_embed_field.provider_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getMediaType(FormStateInterface $form_state) {
    $media_type = parent::getMediaType($form_state);
    if (!$media_type->getSource() instanceof VideoEmbedField) {
      throw new \InvalidArgumentException('Can only add media types which use an VideoEmbed source plugin.');
    }
    return $media_type;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildInputElement(array $form, FormStateInterface $form_state) {
    $form['#attributes']['class'][] = 'media-library-add-form--oembed';

    $media_type = $this->getMediaType($form_state);
    $field_config = $media_type->getSource()->getSourceFieldDefinition($media_type);
    $allowed_providers = $field_config->getSetting('allowed_providers');
    // If no allowed_providers then allow all providers. "Empty" could be 
    // either `[]` or an array of providers with none selected, like 
    // `['provider1' => '0', 'provider2' => '0']`. If there is no intersection of 
    // keys and values, then there are no selected providers.
    $allow_all_providers = empty(array_intersect(array_keys($allowed_providers), array_values($allowed_providers)));
    $providers = [];
    foreach ($this->providerManager->getProvidersOptionList() as $id => $title) {
      if (in_array($id, $allowed_providers, TRUE) || $allow_all_providers) {
        $providers[] = $title->render();
      }
    }

    // Add a container to group the input elements for styling purposes.
    $form['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['media-library-add-form__input-wrapper'],
      ],
    ];

    $form['container']['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Add @type via URL', [
        '@type' => $this->getMediaType($form_state)->label(),
      ]),
      '#description' => $this->t('Allowed providers: @providers.', [
        '@providers' => implode(', ', $providers),
      ]),
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'https://',
        'class' => ['media-library-add-form-oembed-url'],
      ],
    ];

    $form['container']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#button_type' => 'primary',
      '#validate' => ['::validateUrl'],
      '#submit' => ['::addButtonSubmit'],
      // @todo Move validation in https://www.drupal.org/node/2988215
      '#ajax' => [
        'callback' => '::updateFormCallback',
        'wrapper' => 'media-library-wrapper',
        // Add a fixed URL to post the form since AJAX forms are automatically
        // posted to <current> instead of $form['#action'].
        // @todo Remove when https://www.drupal.org/project/drupal/issues/2504115
        //   is fixed.
        'url' => Url::fromRoute('media_directories_ui.media.add'),
        'options' => [
          'query' => [
            'media_type' => ($form_state->get('media_type') ? $form_state->get('media_type')->id() : $form_state->get('selected_type')),
            FormBuilderInterface::AJAX_FORM_REQUEST => TRUE,
          ],
        ],
      ],
      '#attributes' => [
        'class' => ['media-library-add-form-oembed-submit'],
      ],
    ];
    return $form;
  }

  /**
   * Validates the VideoEmbed URL.
   *
   * @param array $form
   *   The complete form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   */
  public function validateUrl(array &$form, FormStateInterface $form_state) {
    $url = $form_state->getValue('url');
    if ($url) {
      $media_type = $this->getMediaType($form_state);
      $field_config = $media_type->getSource()->getSourceFieldDefinition($media_type);
      $allowed_providers = $field_config->getSetting('allowed_providers');
      $allow_all_providers = empty(array_intersect(array_keys($allowed_providers), array_values($allowed_providers)));
      $providers = [];
      foreach ($this->providerManager->getProvidersOptionList() as $id => $title) {
        if (in_array($id, $allowed_providers, TRUE) || $allow_all_providers) {
          $providers[] = $id;
        }
      }
      $provider = $this->providerManager->loadProviderFromInput($url);
      if (empty($provider)) {
        $form_state->setErrorByName('url', $this->t('Could not find a video provider to handle the given URL.'));
      }
      elseif (!in_array($provider->getPluginId(), $providers, TRUE)) {
        $form_state->setErrorByName('url',
          $this->t('Videos from %provider are not permitted for this video embed field. Allowed providers: @providers.', [
            '%provider' => $provider->getPluginDefinition()['title'],
            '@providers' => implode(', ', $providers),
          ])
        );
      }
    }
  }

  /**
   * Submit handler for the add button.
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function addButtonSubmit(array $form, FormStateInterface $form_state) {
    $this->processInputValues([$form_state->getValue('url')], $form, $form_state);
  }

}
