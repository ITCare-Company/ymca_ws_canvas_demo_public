<?php

namespace Drupal\y_camp\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an LB Camp Info block.
 *
 * @Block(
 *   id = "y_camp_info",
 *   admin_label = @Translation("Camp info block"),
 *   category = @Translation("Camp blocks"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       required = FALSE,
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the node, which should be displayed in block."),
 *     ),
 *   }
 * )
 */
class CampInfoBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The node entity.
   *
   * @var \Drupal\node\NodeInterface|null
   */
  private mixed $node;
  
  /**
   * Constructs a new CampInfoBlock instance.
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
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   *   The current route match .
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    CurrentRouteMatch $currentRouteMatch,
    LanguageManagerInterface $languageManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form =  parent::buildConfigurationForm($form, $form_state);
    $form['context_mapping']['node']['#default_value'] = 'layout_builder.entity';
    $form['context_mapping']['node']['#access'] = FALSE;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->currentRouteMatch->getParameter('node');
    if(!$node) {
      $node = $this->getContextValue('node');
    }

    $this->node = $node;

    if (!$this->node instanceof NodeInterface) {
      return [];
    }

    $lang_code = $this->languageManager->getCurrentLanguage()->getId();
    if ($this->node->hasTranslation($lang_code)) {
      $this->node = $this->node->getTranslation($lang_code);
    }

    $build['camp_phone'] = [
      '#type' => 'inline_template',
      '#template' => '<a href="tel:{{ camp_phone }}"><i class="fa fa-phone" aria-hidden="true"></i> {{ camp_phone }} </a>',
      '#context' => [
        'camp_phone' => $this->node->hasField('field_location_phone') ? $this->node->get('field_location_phone')->get(0)?->getString() : '',
      ],
    ];

    $build['camp_address'] = [
      '#type' => 'inline_template',
      '#template' => '<i class="fas fa-map-marker-alt"></i> <div>{{ camp_address }}</div>',
      '#context' => [
        'camp_address' => $this->getAddress(),
      ],
    ];

    if ($this->node->hasField('field_location_directions') && $directions_field_url = $this->node->get('field_location_directions')->first()) {
      $build['camp_direction'] = [
        '#type' => 'inline_template',
        '#template' => '<a href="{{ camp_direction_uri }}" target="_blank"> {{ camp_direction_label }} </a>',
        '#context' => [
          'camp_direction_uri' => $directions_field_url->getUrl()->toString(),
          'camp_direction_label' => $directions_field_url->title ?: $this->t('Directions'),
        ],
      ];
    }

    if ($this->node->hasField('field_location_fax') && $location_fax = $this->node->get('field_location_fax')->get(0)?->getString()) {
      $build['camp_fax'] = [
        '#type' => 'inline_template',
        '#template' => '<a href="tel:{{ camp_fax }}"><i class="fa fa-fax" aria-hidden="true"></i> {{ camp_fax }} </a>',
        '#context' => [
          'camp_fax' => $location_fax,
        ],
      ];
    }

    if ($this->node->hasField('field_location_email') && $camp_email = $this->node->get('field_location_email')->get(0)?->getString()) {
      $build['camp_email'] = [
        '#type' => 'inline_template',
        '#template' => '<a href="mailto:{{ camp_email }}"><i class="fas fa-envelope"></i> {{ camp_email }} </a>',
        '#context' => [
          'camp_email' => $camp_email,
        ],
      ];
    }

    return $build;
  }

  /**
   * Returns formatted Camp address.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   */
  protected function getAddress() {
    $address = $this->node->hasField('field_location_address') ? $this->node->get('field_location_address')->first() : '';
    if ($address) {
      $address_array = $address->toArray();
      $location_address = "{$address_array['address_line1']}, {$address_array['locality']}<br /> {$address_array['administrative_area']} {$address_array['postal_code']}";
      return Markup::create($location_address);
    }
    return $this->t('No Address entered');
  }

}
