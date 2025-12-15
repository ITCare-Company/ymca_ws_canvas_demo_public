<?php

namespace Drupal\ws_event\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an Event Contacts block.
 *
 * @Block(
 *   id = "lb_event_contacts",
 *   admin_label = @Translation("Event Contacts"),
 *   category = @Translation("Event blocks")
 * )
 */
class EventContactsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Current node.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $node;

  /**
   * Node from field_location_reference field.
   *
   * @var \Drupal\node\Entity\Node
   */
  protected $location;

  /**
   * Constructs a new EventLocationsBlock instance.
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
   *   The current route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $currentRouteMatch) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->node = $currentRouteMatch->getParameter('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (!$this->node) {
      return [];
    }

    $build['#contacts'] = [];

    // Check if the node has the fields before accessing them.
    $overrideContact = ($this->node->hasField('field_contact_name') && !$this->node->field_contact_name->isEmpty()) ||
      ($this->node->hasField('field_contact_phone') && !$this->node->field_contact_phone->isEmpty()) ||
      ($this->node->hasField('field_contact_email') && !$this->node->field_contact_email->isEmpty());

    if ($overrideContact) {
      $locationName = $this->node->get('field_contact_name')->get(0)?->getString() ?? '';
      $phone = $this->node->get('field_contact_phone')->get(0)?->getString() ?? '';
      $email = $this->node->get('field_contact_email')->get(0)?->getString() ?? '';
      $build['#contacts'][] = [
        '#location_name' => $locationName,
        '#phone' => $phone,
        '#fax' => $this->getLocationField('field_location_fax'),
        '#email' => !empty($email) ? Link::fromTextAndUrl(
          $email,
          Url::fromUri('mailto:' . $email,
            ['attributes' => ['target' => '_blank']])
        ) : '',
      ];
    }
    else {
      if ($this->node->hasField('field_location_reference')) {
        foreach ($this->node->get('field_location_reference')
          ->referencedEntities() as $entity) {
          $locationName = $this->getLocationField('title', $entity);
          $phone = $this->getLocationField('field_location_phone', $entity);
          $email = $this->getLocationField('field_location_email', $entity);

          $build['#contacts'][] = [
            '#location_name' => $locationName,
            '#phone' => $phone,
            '#fax' => $this->getLocationField('field_location_fax'),
            '#email' => !empty($email) ? Link::fromTextAndUrl(
              $email,
              Url::fromUri('mailto:' . $email,
                ['attributes' => ['target' => '_blank']])
            ) : '',
          ];
        }
      }
    }

    return $build;
  }

  /**
   * Get field from location (related node)
   *
   * @param string $fieldName
   *   The location field.
   * @param null|NodeInterface $location
   *   The location node.
   *
   * @return string
   *   The location field value.
   */
  protected function getLocationField($fieldName, $location = NULL) {

    if (!$location) {
      if (!$this->location) {
        if (!$this->node->hasField('field_location_reference') ||
          $this->node->get('field_location_reference')->isEmpty()) {
          return '';
        }
        $this->location = $this->node->get('field_location_reference')->entity;
      }
      $location = $this->location;
    }

    return $location->hasField($fieldName) ? $location->$fieldName->value : '';
  }

}
