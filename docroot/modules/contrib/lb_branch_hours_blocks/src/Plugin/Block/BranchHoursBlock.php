<?php

namespace Drupal\lb_branch_hours_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\node\NodeInterface;
use Drupal\y_branch\BranchHoursHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an LB Branch Hours block.
 *
 * @Block(
 *   id = "lb_branch_hours",
 *   admin_label = @Translation("Branch Hours"),
 *   category = @Translation("Branch blocks"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       required = FALSE,
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the node, which should be displayed in block."),
 *     ),
 *   }
 * )
 */
class BranchHoursBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Location node
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * Current node
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node_current;

  /**
   * Branch Hours Helper service.
   *
   * @var \Drupal\y_branch\BranchHoursHelper
   */
  protected $branchHoursHelper;

  /**
   * Constructs a new BranchHours instance.
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
   * @param \Drupal\y_branch\BranchHoursHelper $branchHoursHelper
   *   Branch Hours Helper service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $currentRouteMatch, BranchHoursHelper $branchHoursHelper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->branchHoursHelper = $branchHoursHelper;
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
      $container->get('y_branch.hours_helper')

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

    if ($node instanceof NodeInterface) {
      if ($node->hasField('field_branch_hours')) {
        $this->node = $node;
      }
      else if ($node->hasField('field_location_reference') && !$node->get('field_location_reference')->isEmpty()) {
        $entities = $node->get('field_location_reference')->referencedEntities();
        $this->node = reset($entities);
      }
      else if ($node->hasField('field_facility_loc') && !$node->get('field_facility_loc')->isEmpty()) {
        $this->node_current = $node;
        $entities = $node->get('field_facility_loc')->referencedEntities();
        $this->node = reset($entities);
      }
    }

    if (!$this->node instanceof NodeInterface) {
      return [];
    }

    if ($this->node_current &&
      $this->node_current->hasField('field_branch_hours') &&
      !$this->node_current->get('field_branch_hours')->isEmpty()) {
      $branch_hours = $this->branchHoursHelper->getBranchHours($this->node_current);
      $holiday_hours = $this->branchHoursHelper->getBranchHolidayHours($this->node_current);
    }
    else {
      $branch_hours = $this->branchHoursHelper->getBranchHours($this->node);
      $holiday_hours = $this->branchHoursHelper->getBranchHolidayHours($this->node);
    }

    $build = [
      'branch_name' => [
        '#type' => 'inline_template',
        '#template' => '<h1>{{ branch_name }}</h1>',
        '#context' => [
          'branch_name' => $node->label(),
        ],
      ],
      'branch_phone' => [
        '#type' => 'inline_template',
        '#template' => '<a href="tel:{{ branch_phone }}"><i class="fa fa-phone" aria-hidden="true"></i><div>{{ branch_phone }}</div></a>',
        '#context' => [
          'branch_phone' => $this->getOverriddenField('field_location_phone')->value,
        ],
      ],
      'branch_fax' => [
        '#type' => 'inline_template',
        '#template' => '{% if branch_fax  %}<a href="tel:{{ branch_fax }}"><i class="fa fa-phone" aria-hidden="true"></i><div>{{ branch_fax }}</div></a> {% endif %}',
        '#context' => [
          'branch_fax' => $this->getOverriddenField('field_location_fax')->value,
        ],
      ],
      'branch_address' => [
        '#type' => 'inline_template',
        '#template' => '<i class="fas fa-map-marker-alt"></i><div>{{ branch_address }}</div>',
        '#context' => [
          'branch_address' => $this->getAddress(),
        ],
      ],
      'branch_email' => [
        '#type' => 'inline_template',
        '#template' => '{% if branch_email %}<a href="mailto:{{ branch_email }}"><i class="fas fa-envelope"></i><div>{{ branch_email }}</div></a>{% endif %}',
        '#context' => [
          'branch_email' => $this->getOverriddenField('field_location_email')->value,
        ],
      ],
      'branch_today_hours' => [
          '#lazy_builder' => [
            'lb_branch_hours_blocks.hours_today:generateHoursToday',
            [$this->node->id()],
          ],
          '#create_placeholder' => TRUE,
      ],
      'branch_hours' => [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h5',
          '#value' => $branch_hours['label'],
        ],
        'table' => $branch_hours['table'],
      ],
      'branch_holiday_hours' => [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h5',
          '#value' => $holiday_hours['label'],
        ],
        'table' => $holiday_hours['table'],
      ],
      'branch_hours_link' => $this->getBranchHoursLink(),
      '#attached' => [
        'library' => [
          'lb_branch_hours_blocks/hours_today',
        ],
        'drupalSettings' => [
          'lb_branch_hours_blocks' => [
            'branch_hours' => $branch_hours['js_settings'],
            'exceptions' => $holiday_hours['js_settings'],
            'tz' => $this->branchHoursHelper->getTimezone(),
          ]
        ]
      ],
    ];
    return $build;
  }

  /**
   * Returns formatted Location address.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   */
  protected function getAddress() {
    $address = $this->getOverriddenField('field_location_address')->first();
    if ($address) {
      $address_array = $address->toArray();
      $location_address = "{$address_array['address_line1']}, {$address_array['locality']}<br /> {$address_array['administrative_area']} {$address_array['postal_code']}";
      return Markup::create($location_address);
    }
    return '';
  }

  /**
   * Returns More Hours link render array.
   *
   * @return array
   */
  protected function getBranchHoursLink() {
    if ($this->node->hasField('field_more_hours_link')) {
      return $this->node->field_more_hours_link->view(['label' => 'hidden']);
    }
    return [];
  }

  /**
   * Get the field from the current node if is not empty or from a related location node
   *
   * @param string $fieldName
   * @return FieldItemListInterface field value
   */
  protected function getOverriddenField($fieldName) {
      if ($this->node_current &&
      $this->node_current->hasField($fieldName) &&
      !$this->node_current->get($fieldName)->isEmpty()) {
      return $this->node_current->get($fieldName);
    }

    return $this->node->get($fieldName);
  }

}
