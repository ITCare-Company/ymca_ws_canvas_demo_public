<?php

namespace Drupal\ws_event;

use Drupal\smart_date_recur\SmartDateRecurManager;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityDisplayRepository;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\daterange_compact\DateRangeCompactFormatterInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\node\NodeInterface;
use Drupal\smart_date\Plugin\Field\FieldType\SmartDateFieldItemList;
use Drupal\smart_date\Plugin\Field\FieldType\SmartDateItem;
use Drupal\smart_date\SmartDateManager;

/**
 * Helper service to work with Event LB data.
 */
class EventDataHelper {

  /**
   * Daterange Compact formats to be used with Events.
   */
  const DATE_RANGE_MEDIUM_FORMAT = 'medium_date_comma_separated';
  const DATE_RANGE_TIME_FORMAT = 'medium_time_only';
  const SMART_DATE_DEFAULT = 'compact';
  const SMART_DATE_TIME_FORMAT = 'time_only';

  use StringTranslationTrait;

  /**
   * Daterange Compact formatter service.
   *
   * @var \Drupal\daterange_compact\DateRangeCompactFormatterInterface
   */
  protected $daterangeCompact;

  /**
   * Smart Date manager service.
   *
   * @var \Drupal\smart_date\SmartDateManager
   */
  protected SmartDateManager $smartDateManager;

  /**
   * Module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected ModuleHandler $moduleHandler;

  /**
   * Entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepository
   */
  protected EntityDisplayRepository $entityDisplayRepository;

  /**
   * Entity type manager interface.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new EventDataHelper.
   *
   * @param \Drupal\daterange_compact\DateRangeCompactFormatterInterface $daterange_compact
   *   Daterange Compact formatter service.
   * @param \Drupal\smart_date\SmartDateManager $smartDateManager
   *   SmartDate manager service.
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   Module handler service.
   * @param \Drupal\Core\Entity\EntityDisplayRepository $entityDisplayRepository
   *   Entity Display repository.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   */
  public function __construct(
    DateRangeCompactFormatterInterface $daterange_compact,
    SmartDateManager $smartDateManager,
    ModuleHandler $moduleHandler,
    EntityDisplayRepository $entityDisplayRepository,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    $this->daterangeCompact = $daterange_compact;
    $this->smartDateManager = $smartDateManager;
    $this->moduleHandler = $moduleHandler;
    $this->entityDisplayRepository = $entityDisplayRepository;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Get formatted date range string.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   * @param string $format
   *   Format to be formatted to.
   *
   * @return string
   *   Formatted date string.
   */
  public function getFormattedDateRange(
    NodeInterface $node,
    string $format = self::DATE_RANGE_MEDIUM_FORMAT
  ): string {
    if (!$node->hasField('field_event_dates') || !$node->hasField('field_event_dates_smart')) {
      return '';
    }

    // Get dates.
    $start_date = $node->field_event_dates->value;
    $end_date = $node->field_event_dates->end_value ?: $start_date;
    $timezone = date_default_timezone_get();

    $start = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $start_date, DateTimeItemInterface::STORAGE_TIMEZONE);
    $start->setTimezone(timezone_open($timezone));

    $end = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $end_date, DateTimeItemInterface::STORAGE_TIMEZONE);
    $end->setTimezone(timezone_open($timezone));

    if ($this->moduleHandler->moduleExists('smart_date') && $format == self::SMART_DATE_DEFAULT) {
      return $this->smartDateManager->formatSmartDate($start->getTimestamp(), $end->getTimestamp(), $format, NULL, 'string');
    }

    return (string) $this->daterangeCompact
      ->formatDateRange((string) $start, (string) $end, $format);
  }

  /**
   * Get formatted smart date range string.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   * @param string $format
   *   Format to be formatted to.
   *
   * @return array
   *   Formatted date render array.
   */
  public function getFormattedSmartDateRange(
    NodeInterface $node,
    string $format = self::SMART_DATE_DEFAULT
  ): array {
    if (!$node->hasField('field_event_dates_smart')) {
      return [];
    }

    $next = $this->getNextInstance($node->field_event_dates_smart);

    if (!$next instanceof SmartDateItem) {
      return [];
    }

    $view_modes = $this->entityDisplayRepository->getViewModes('node');

    if (array_key_exists($format, $view_modes)) {
      return $next->view($format);
    }

    return $next->view([
      'type' => 'smartdate_default',
      'settings' => [
        'format' => 'compact',
      ],
      'third_party_settings' => $this->getThirdPartySettings(),
    ]);
  }

  /**
   * Get the upcoming instance of a recurring smart date field.
   *
   * @param \Drupal\smart_date\Plugin\Field\FieldType\SmartDateFieldItemList $dates
   *   A list of smart date items.
   *
   * @return \Drupal\smart_date\Plugin\Field\FieldType\SmartDateItem|null
   *   The upcoming instance in the list.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function getNextInstance(SmartDateFieldItemList $dates): SmartDateItem|null {
    $instances = [];
    foreach ($dates as $instance) {
      $instances[] = $instance;
    }
    $next_instance = (new SmartDateRecurManager)->findNextInstance($instances);
    // Return the next event instance if there is one,
    // otherwise the event is in the past so return the first instance.
    return ($next_instance >= 0) ? $instances[$next_instance] : $dates->first();
  }

  /**
   * Return default third party settings for date augmenter.
   *
   * @param string $type
   *   Either "single" or "recur".
   *
   * @return array
   *   The settings.
   */
  public function getThirdPartySettings(string $type = 'single'): array {
    $third_party_settings = $this->entityTypeManager->getStorage('entity_view_display')->load('node.lb_event.event_info')->getRenderer('field_event_dates_smart')->getThirdPartySettings();

    $date_augmenter_settings = $third_party_settings['date_augmenter'];

    // If this is a recurring display, set the instance and the rule settings.
    if ($type == 'recur') {
      $recur_settings = [
        'instances' => $date_augmenter_settings,
        'rule' => $date_augmenter_settings,
      ];
      $recur_settings['rule']['settings']['addtocal']['label'] = 'Add all to Calendar';

      $date_augmenter_settings = $recur_settings;
    }

    return ['date_augmenter' => $date_augmenter_settings];
  }

  /**
   * Get the name of a location.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to pull from.
   * @param string $view_mode
   *   The view mode to use.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   A translatable string with the location name.
   */
  public function getLocationName(NodeInterface $node, string $view_mode = 'default') {
    // If there is a custom address, use it.
    if (!$node->field_location_address->isEmpty()) {
      // If there is a title for the address, use it.
      if ($node->field_location_address->organization) {
        return $node->field_location_address->organization;
      }
      // Try the first line of the address as a backup.
      elseif ($node->field_location_address->address_line1) {
        return $node->field_location_address->address_line1;
      }
    }
    elseif (!$node->field_location_reference->isEmpty()) {
      // If more than one location, then show "Multiple locations".
      if ($node->field_location_reference->count() > 1) {
        return $this->t('Multiple locations');
      }
      else {
        // If only one location, then display it.
        return $node->field_location_reference->first()->view($view_mode);
      }
    }
    // If all else fails...
    return '';
  }

  /**
   * Get the location from either the address or reference.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to search.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getLocationAddressString(NodeInterface $node) {
    // If there is a custom address set, use it.
    if (!$node->field_location_address->isEmpty()) {
      $locTitle = $node->field_location_address->organization;
      $locAddress = str_replace('<br />', ', ', $this->getAddress($node));
    }
    else {
      // Otherwise, check for a location.
      $locationField = $node->get('field_location_reference')->getValue();
      foreach ($locationField as $locationFieldItem) {
        $location = $this->entityTypeManager->getStorage('node')
          ->load($locationFieldItem['target_id']);
        if ($location instanceof NodeInterface) {
          $locTitle = $location->getTitle();
          $locAddress = str_replace(
            '<br />',
            ', ',
            $this->getAddress($location)
          );
        }
      }
    }

    $datesAtc['atc_location'][] = implode(
      ', ',
      array_filter([$locTitle, $locAddress])
    );
  }

}
