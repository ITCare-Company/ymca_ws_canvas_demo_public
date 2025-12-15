<?php

namespace Drupal\y_camp\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an LB Camp Back Link block.
 *
 * @Block(
 *   id = "y_camp_back_link",
 *   admin_label = @Translation("Camp Back Link"),
 *   description = @Translation("A block that displaying on the Camp and Camp LP node on the top left area.And usually it leads to the All Locations page."),
 *   category = @Translation("Camp blocks")
 * )
 */
class CampBackLinkBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new CampBackLinkBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'camp_backlink_title' => '',
      'camp_backlink_url' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $form['link'] = [
      '#type' => 'details',
      '#title' => $this->t('Camps back link'),
      '#description' => $this->t('This link is displaying on the top left corner of the Camp node if you use Layout Builder for these nodes.'),
      '#open' => TRUE,
    ];

    $form['link']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Camp Backlink title'),
      '#default_value' => $config['camp_backlink_title'] ?? '',
      '#description' => $this->t('The title of the Camp back link. By default uses the Site name.'),
    ];

    $form['link']['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Camp Backlink Url'),
      '#default_value' => $config['camp_backlink_url'] ?? '/locations',
      '#description' => $this->t('The URL for the Camp back link. This URL should starts from "/".'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $back_link = $form_state->getValue('link');
    $this->configuration['camp_backlink_title'] = $back_link['title'];
    $url = trim($back_link['url']);
    if (!str_starts_with($url, '/')) {
      $url =  '/' . $url;
    }
    $this->configuration['camp_backlink_url'] = $url;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $title = !empty($this->configuration['camp_backlink_title'])
      ? $this->configuration['camp_backlink_title']
      : $this->configFactory->get('y_camp.settings')->get('link.title');
    $url = !empty($this->configuration['camp_backlink_url'])
      ? $this->configuration['camp_backlink_url']
      : $this->configFactory->get('y_camp.settings')->get('link.url');
    $build['back_link'] = [
      '#type' => 'inline_template',
      '#template' => '<a href="{{ back_link_url }}"><span class="fa fa-chevron-left fa-xs" aria-hidden="true"></span>{{ back_link_label }}</a>',
      '#context' => [
        'back_link_label' => $title,
        'back_link_url' => Url::fromUserInput(
          $url,
          ['absolute' => TRUE])->toString()
      ],
    ];

    return $build;
  }

}
