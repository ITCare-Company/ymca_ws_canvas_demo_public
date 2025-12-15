<?php

namespace Drupal\y_branch_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides Branch Menu block.
 *
 * @Block(
 *   id = "y_branch_menu",
 *   admin_label = @Translation("Branch Menu Block"),
 *   category = @Translation("Branch blocks")
 * )
 */
class BranchMenuBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
  protected $entity;

  /**
   * The current path.
   *
   * @var string
   */
  private string $currentPath;

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
   * @param CurrentRouteMatch $currentRouteMatch
   *   The current route match service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $currentRouteMatch, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $currentRouteMatch;
    $this->entity = $currentRouteMatch->getParameter('node');
    $this->currentPath = $request_stack->getCurrentRequest()->getPathInfo();
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
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'home_link' => TRUE,
      'location' => '',
      'limit_links' => 6,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (!($node = $this->entity) || $this->inPreview) {
      return ['#in_preview' => $this->t('Branch Menu: To see your changes in this block, please save the layout.')];
    }
    $config = $this->configuration;

    // If we're on a Article/Event page, check the location reference first.
    if ($node->hasField('field_location_reference') &&
      $node->get('field_location_reference')->isEmpty()) {
      $referencedEntities = $node->get('field_location_reference')->referencedEntities();
      $node = reset($referencedEntities);
    }
    // If a location is set in the block config, instead of the location ref.
    if ($config['location']) {
      $node = Node::load($config['location']);
    }
    // Try to get the menu links from whatever node we picked.
    if ($node->hasField('field_branch_menu_links')) {
      $fieldLinks = $node->get('field_branch_menu_links');
    }

    if (empty($fieldLinks) || $fieldLinks->isEmpty()) {
      return [];
    }

    $result = [];

    if ($config['home_link']) {
      $result[] = $this->getHomeLink($node);
      $config['limit_links']--;
    }

    array_push($result, ...$this->getLinks($fieldLinks, $config['limit_links']));
    return ['#links' => $result];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->configuration;

    $form['home_link'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show "Home" link'),
      '#default_value' => $config['home_link'],
    ];

    // Dig deep into the route to get the parent node.
    $node = $this->currentRouteMatch->getParentRouteMatch()?->getParameter('section_storage')?->getContextValue('entity');
    // Determine if $node is a Branch.
    $is_branch = $node?->getType() == 'branch';

    // If the block is being placed on a Branch, then the location should be set
    // to the parent page and should not be editable.
    $form['location'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Location'),
      '#description' => $this->t('Choose the branch from which to pull menu links. If the block is being placed on a Branch page, this option is not configurable.'),
      '#default_value' => (empty($config['location']) && $is_branch) ? $node : Node::load($config['location']),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['branch'],
        'sort' => ['title' => 'DESC']],
      '#attributes' => ['readonly' => $is_branch]
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['home_link'] = $form_state->getValue('home_link');
    $this->configuration['location'] = $form_state->getValue('location');
  }

  /**
   * Build home link which redirect to branch page.
   *
   * @param \Drupal\node\NodeInterface $node
   *
   * @return array|Link
   */
  private function getHomeLink($node) {
    $url = $node->toUrl('canonical', ['attributes' => ['class' => ['home']], 'set_active_class' => TRUE]);
    return Link::fromTextAndUrl($this->t('Branch Home'), $url);
  }

  /**
   * @param $fieldBranchMenuLinks
   * @param int $limit number of links returned
   * @return array links
   */
  private function getLinks($fieldBranchMenuLinks, int $limit) {
    $result = [];
    if ($values = $fieldBranchMenuLinks->getValue()) {
      foreach ($values as $value) {
        if (count($result) >= $limit) {
          break;
        }

        $url = Url::fromUri($value['uri'], $value['options'] + ['set_active_class' => TRUE]);
        $result[] = Link::fromTextAndUrl($value['title'], $url);
      }
    }
    return $result;
  }
}
