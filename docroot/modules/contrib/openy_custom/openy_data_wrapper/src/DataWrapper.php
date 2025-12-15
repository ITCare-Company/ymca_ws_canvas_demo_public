<?php

namespace Drupal\openy_data_wrapper;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\openy_socrates\OpenyDataServiceInterface;
use Drupal\openy_socrates\OpenySocratesFacade;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class DataWrapper.
 *
 * Provides data for location finder add membership calc.
 */
class DataWrapper implements OpenyDataServiceInterface {

  /**
   * Openy Socrates Facade.
   *
   * @var \Drupal\openy_socrates\OpenySocratesFacade
   */
  protected $socrates;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $loggerChannel;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * DataWrapperBase constructor.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   * @param \Drupal\openy_socrates\OpenySocratesFacade $socrates
   *   Socrates.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $loggerChannel
   *   Logger channel.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   Language manager.
   */
  public function __construct(RendererInterface $renderer, EntityTypeManagerInterface $entityTypeManager, OpenySocratesFacade $socrates, CacheBackendInterface $cacheBackend, LoggerChannelInterface $loggerChannel, ConfigFactoryInterface $configFactory, LanguageManagerInterface $languageManager) {
    $this->renderer = $renderer;
    $this->entityTypeManager = $entityTypeManager;
    $this->socrates = $socrates;
    $this->cacheBackend = $cacheBackend;
    $this->loggerChannel = $loggerChannel;
    $this->configFactory = $configFactory;
    $this->languageManager = $languageManager;
  }

  /**
   * Get all location pins for map.
   *
   * Used in location finder block.
   */
  public function getLocationPins() {
    $activeTypes = \Drupal::configFactory()->get('openy_map.settings')->get('active_types');
    $activeTypes = !empty($activeTypes) ? array_keys(array_filter($activeTypes)) : [];
    $pins = [];
    foreach ($activeTypes as $type) {
      $typePins = $this->getPins($type);
      $pins = array_merge($pins, $typePins);
    }
    return $pins;
  }

  /**
   * Get pins.
   *
   * @param string $type
   *   Location node type (branch, camp or facility).
   * @param int $id
   *   Node ID (if set return pin only for specified node ID).
   *
   * @return array
   *   Pins.
   */
  public function getPins($type, $id = NULL) {
    if ($id) {
      $location_ids[] = $id;
    }
    else {
      $langcode = $this->languageManager->getCurrentLanguage()->getId();
      $location_ids = $this->entityTypeManager->getStorage('node')
        ->getQuery()
        ->accessCheck(FALSE)
        ->condition('type', $type)
        ->condition('status', 1)
        ->condition('langcode', $langcode)
        ->execute();
    }

    if (!$location_ids) {
      return [];
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $builder = $this->entityTypeManager->getViewBuilder('node');
    $locations = $storage->loadMultiple($location_ids);

    // Get labels and icons for every bundle from OpenY Map config.
    $typeIcons = $this->configFactory->get('openy_map.settings')->get('type_icons');
    $typeLabels = $this->configFactory->get('openy_map.settings')->get('type_labels');
    $tag = $typeLabels[$type];
    $pins = [];
    foreach ($locations as $location) {
      $view = $builder->view($location, 'teaser');
      $coordinates = $location->get('field_location_coordinates')->getValue();
      if (!$coordinates) {
        continue;
      }
      $uri = !empty($typeIcons[$location->bundle()]) ? $typeIcons[$location->bundle()] :
        '/' . \Drupal::service('extension.list.module')->getPath('openy_map') . "/img/map_icon_green.png";
      $pins[] = [
        'icon' => $uri,
        'tags' => [$tag],
        'lat' => round($coordinates[0]['lat'], 5),
        'lng' => round($coordinates[0]['lng'], 5),
        'name' => $location->label(),
        'markup' => $this->renderer->renderRoot($view),
      ];
    }

    return $pins;
  }

  /**
   * Get list of membership types.
   *
   * Used in membership calc block.
   *
   * @return array
   *   The list of membership types keyed by type ID.
   */
  public function getMembershipTypes() {
    $types = [];
    $membership_ids = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'membership')
      ->condition('status', 1)
      ->execute();

    if (!$membership_ids) {
      return $types;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $memberships = $storage->loadMultiple($membership_ids);

    foreach ($memberships as $membership) {
      $membership_id = $membership->id();
      $types[$membership_id] = [
        'title' => $membership?->label(),
        'image' => $this->getImage($membership),
        'description' => $this->getDescription($membership),
      ];
    }

    return $types;
  }

  /**
   * Get Branch location pins for map.
   *
   * Used in membership calc block.
   */
  public function getBranchPins($id = NULL) {
    return $this->getPins('branch', $id);
  }

  /**
   * Gets value for image field of template.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An object of the Membership node.
   *
   * @return array|null
   *   A render array for the image or NULL if no image is found.
 */
  private function getImage(EntityInterface $entity): mixed {
    $media = $entity->get('field_mbrshp_image')->referencedEntities();
    $media = reset($media);
    if (!$media) {
      return NULL;
    }

    $builder = $this->entityTypeManager->getViewBuilder('media');
    return $builder->view($media, 'calc_preview');
  }

  /**
   * Gets value for description field of template.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An object of the Membership node.
   *
   * @return array|null
   *   A render array for the description or NULL if no description is found.
 */
  private function getDescription(EntityInterface $entity): mixed {
    if ($entity->get('field_mbrshp_description')->isEmpty()) {
      return NULL;
    }

    $value = $entity->get('field_mbrshp_description')->first()->getValue();
    return check_markup($value['value'], $value['format']);
  }

  /**
   * Get the list of locations.
   *
   * Used in membership calc block.
   *
   * @return array
   *   The list of locations keyed by location ID.
   */
  public function getLocations() {
    $data = [];
    $langcode = $this->languageManager->getCurrentLanguage()->getId();

    $location_ids = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'branch')
      ->condition('langcode', $langcode)
      ->condition('status', 1)
      ->addTag('data_wrapper_locations')
      ->execute();

    if (!$location_ids) {
      return [];
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $locations = $storage->loadMultiple($location_ids);

    foreach ($locations as $location) {
      $data[$location->id()] = [
        'title' => $location->label(),
      ];
    }

    return $data;
  }

  /**
   * Get Summary.
   *
   * @param int $location_id
   *   Location ID.
   * @param string $membership_id
   *   Membership type ID.
   *
   * @return string
   *   Price.
   */
  public function getSummary($location_id, $membership_id) {
    $storage = $this->entityTypeManager->getStorage('node');
    $builder = $this->entityTypeManager->getViewBuilder('node');
    $location = $storage->load($location_id);
    $result['location'] = $builder->view($location, 'calc_summary');
    $membership = $storage->load($membership_id);
    $result['membership'] = $builder->view($membership, 'calc_summary');

    $info = $membership->field_mbrshp_info->referencedEntities();
    $defaultUrl = Url::fromRoute('<front>');
    foreach ($info as $value) {
      if (!empty($value->field_mbrshp_location->first()) && $value->field_mbrshp_location->first()->get('target_id')->getValue() == $location_id) {
        $result['price']['monthly_rate'] = $value->field_mbrshp_monthly_rate->value;
        $result['price']['join_fee'] = $value->field_mbrshp_join_fee->value;
        $result['link'] = !empty($value->field_mbrshp_link->first()) ? $value->field_mbrshp_link->first()->getUrl() : $defaultUrl;
      }
    }

    return $result;
  }

  /**
   * Get Redirect Link.
   *
   * @param int $location_id
   *   Location ID.
   * @param string $membership_id
   *   Membership type ID.
   *
   * @return \Drupal\Core\Url
   *   Redirect url.
   */
  public function getRedirectUrl($location_id, $membership_id) {
    $storage = $this->entityTypeManager->getStorage('node');
    $membership = $storage->load($membership_id);
    $info = $membership->field_mbrshp_info->referencedEntities();
    $defaultUrl = Url::fromRoute('<front>');
    foreach ($info as $value) {
      if (!empty($value->field_mbrshp_location->first()) && $value->field_mbrshp_location->first()->get('target_id')->getValue() == $location_id) {
        return !empty($value->field_mbrshp_link->first()) ? $value->field_mbrshp_link->first()->getUrl() : $defaultUrl;
      }
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function addDataServices(array $services) {
    return [
      'getLocationPins',
      'getMembershipTypes',
      'getBranchPins',
      'getLocations',
      'getSummary',
      'getRedirectUrl',
    ];
  }

}
