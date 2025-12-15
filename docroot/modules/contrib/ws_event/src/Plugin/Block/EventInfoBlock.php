<?php

namespace Drupal\ws_event\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FormatterPluginManager;
use Drupal\Core\Link;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\smart_date_recur\SmartDateRecurManager;
use Drupal\ws_event\EventDataHelper;
use LinkedIn\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Event Info block.
 *
 * @Block(
 *   id = "lb_event_info",
 *   admin_label = @Translation("Event Info Block"),
 *   category = @Translation("Event blocks")
 * )
 */
class EventInfoBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Cuurent node.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;


  /**
   * Logger channel for the Y-LB.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Event data helper service.
   *
   * @var \Drupal\ws_event\EventDataHelper
   */
  protected $eventDataHelper;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity formatter plugin manager.
   *
   * @var \Drupal\Core\Field\FormatterPluginManager
   */
  protected $formatterPluginManager;

  /**
   * Constructs a new EventInfoBlock instance.
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
   *   The current route match service.
   * @param \Drupal\ws_event\EventDataHelper $eventDataHelper
   *   Event data helper service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   The logger factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Field\FormatterPluginManager $formatterPluginManager
   *   The formatter plugin manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    CurrentRouteMatch $currentRouteMatch,
    EventDataHelper $eventDataHelper,
    LoggerChannelFactoryInterface $loggerFactory,
    EntityTypeManagerInterface $entityTypeManager,
    FormatterPluginManager $formatterPluginManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->eventDataHelper = $eventDataHelper;
    $this->node = $currentRouteMatch->getParameter('node');
    $this->logger = $loggerFactory->get('y_lb');
    $this->entityTypeManager = $entityTypeManager;
    $this->formatterPluginManager = $formatterPluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('ws_event.event_data_helper'),
      $container->get('logger.factory'),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.field.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (!$this->node || !$this->node->hasField('field_location_reference')) {
      return [];
    }

    $locationsInfo = [];
    $force_chronological = FALSE;

    // If the event has a custom address, and it's set, use that.
    if ($this->node->hasField('field_location_address') &&
      ($fieldAddress = $this->node->get('field_location_address')) &&
      !$fieldAddress->isEmpty()) {
      $locationsInfo[] = [
        '#location_link' => $fieldAddress->first()->organization,
        '#location_address' => '',
        '#location_directions' => $this->getDirections($this->node),
      ];
    }
    elseif ($this->node->hasField('field_location_reference') &&
      $location_reference = $this->node->get('field_location_reference')) {
      // If there are location references, iterate through those and show them.
      foreach ($location_reference->referencedEntities() as $entity) {
        $locationsInfo[] = [
          '#location_link' => $this->getLocationLink($entity),
          '#location_address' => '',
          '#location_directions' => $this->getDirections($entity),
        ];
      }
    }

    $smartDateRecur = $this->node->get('field_event_dates_smart');
    if ($smartDateRecur->count()) {
      $smartDate = $this->eventDataHelper->getNextInstance(
        $smartDateRecur)->view('event_info');
    }

    // If there is more than one date, build the recurrence section.
    if ($smartDateRecur->count() <= 1) {
      $recurHeader = '';
    }
    else {
      // Get the current position in the list of instances.
      $instances = [];
      foreach ($smartDateRecur as $instance) {
        $instances[] = $instance;
      }
      $next_instance = (new SmartDateRecurManager)->findNextInstance($instances);
      $total_instances = $smartDateRecur->count();
      $recurHeader = $this->formatPlural($total_instances - $next_instance - 1,
        '1 more date',
        '@count more dates');

      // If there are multiple date values/rules, merge it down into one.
      if (
        $smartDate['#value'] == $smartDateRecur[$next_instance]->value &&
        $smartDate['#end_value'] == $smartDateRecur[$next_instance]->end_value &&
        is_null($smartDateRecur[$next_instance]->rrule)
      ) {
        $force_chronological = TRUE;
      }
    }

    // Build the formatter settings since we need to control force_chrono.
    $recurring_formatter_settings = $this->formatterPluginManager->getDefaultSettings('smartdate_recurring');
    $custom_settings = [
      'force_chronological' => $force_chronological,
      'past_display' => 0,
      'upcoming_display' => 3,
      'show_next' => TRUE,
      'format' => 'compact',
    ];

    $recurring_formatter_settings = array_replace($recurring_formatter_settings, $custom_settings);

    $smartDateDisplaySettings = [
      'type' => 'smartdate_recurring',
      'label' => 'hidden',
      'settings' => $recurring_formatter_settings,
      'third_party_settings' => $this->eventDataHelper->getThirdPartySettings('recur'),
    ];

    $build = [
      '#smart_date' => $smartDate ?? '',
      '#recur_header' => $recurHeader,
      '#smart_date_recur' => $smartDateRecur->view($smartDateDisplaySettings),
      '#locations' => $locationsInfo,
    ];

    return $build;
  }

  /**
   * Returns render array for the Location Directions Link or empty string.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to search.
   *
   * @return array|mixed|string
   *   A render array with location directions or empty string.
   */
  protected function getDirections(NodeInterface $node) {
    if (
      in_array(
        $node->getType(),
        ['branch', 'camp', 'facility', 'location', 'lb_event']
      ) &&
      $node->hasField('field_location_directions')
    ) {
      $directions_title = $this->t('Get Directions');

      // If the direction field is set, that takes priority.
      if (!$node->field_location_directions->isEmpty()) {
        $directions_field_url = $node->field_location_directions->get(0);
        // If directions has a URL set, return a link with it and its title.
        if (
          $directions_field_url &&
          $directions_field_url->getUrl()->toString() !== ""
        ) {
          $title = $directions_field_url->get('title')->getValue() ?: $directions_title;
          return Link::fromTextAndUrl($title, $directions_field_url->getUrl());
        }
      }

      // If directions is not set, use the address field.
      $address = $node->field_location_address->get(0);
      if ($address) {
        $address_array = $address->toArray();
        $location_address = "{$address_array['address_line1']} {$address_array['locality']}, {$address_array['administrative_area']} {$address_array['postal_code']}";
        $directions_url = Url::fromUri('https://www.google.com/maps/dir/', [
          'query' => [
            'api' => 1,
            'destination' => $location_address,
          ],
        ]);

        // If the directions title is not overridden,
        // use the address to give users more context on the page.
        if ($directions_title == '') {
          $directions_title = $location_address;
        }

        $link = Link::fromTextAndUrl($directions_title, $directions_url);
        $link = $link->toRenderable();
        $link['#attributes'] = ['target' => ['_blank']];
        return $link;
      }
    }
    return '';
  }

  /**
   * Returns Location link.
   *
   * @param \Drupal\node\NodeInterface $location
   *   The location node.
   *
   * @return \Drupal\Core\Link
   *   A link from the location.
   */
  protected function getLocationLink(NodeInterface $location) {
    $url = $location->toUrl();
    return Link::fromTextAndUrl($location->label(), $url);
  }

  /**
   * Helper method to build Date/Time strings from node data.
   *
   * @return array
   *   A date array.
   */
  protected function getDateTime() {
    $result = [
      'date' => '',
      'time' => '',
    ];

    $formatDate = 'F j, Y';
    $formatTime = 'g:i a';

    try {
      $startDate = $this->node->field_event_dates->value;
      $endDate = $this->node->field_event_dates->end_value;

      $start = new \DateTimeImmutable($startDate);
      $end = new \DateTimeImmutable($endDate);

      $result['date'] = $start->format($formatDate);
      // Start and end time differs.
      if ($end->diff($start)->h > 0) {
        $result['time'] = $start->format($formatTime) . ' - ' . $end->format($formatTime);
      }
      else {
        $result['time'] = $end->format($formatTime);
      }
    }
    catch (Exception $e) {
      $this->logger->debug($e->getMessage());
    }

    return $result;
  }

}
