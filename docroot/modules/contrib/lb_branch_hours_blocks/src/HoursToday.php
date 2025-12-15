<?php

namespace Drupal\lb_branch_hours_blocks;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\y_branch\BranchHoursHelper;

/**
 * Contains HoursToday class.
 */
class HoursToday implements TrustedCallbackInterface {

  use StringTranslationTrait;

  /**
   * System time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Branch Hours Helper service.
   *
   * @var \Drupal\y_branch\BranchHoursHelper
   */
  protected $branchHoursHelper;

  /**
   * Constructs a HoursToday trusted callback.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\y_branch\BranchHoursHelper $branchHoursHelper
   *   Branch Hours Helper service.
   */
  public function __construct(TimeInterface $time, BranchHoursHelper $branch_hours_helper) {
    $this->time = $time;
    $this->branchHoursHelper = $branch_hours_helper;
  }

  /**
   * Get today hours.
   *
   * @param string $nid
   *   Node ID.
   *
   * @return string[]
   *   Renderable array of today hours.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  public function generateHoursToday($nid) {
    $label = $this->t('Closed');

    // If we are not on a node, then return a placeholder.
    if (is_null($nid)) {
      return [
        '#markup' => '<div class="today">'. $label . '</div>',
      ];
    }

    $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($nid);

    if ($node instanceof NodeInterface) {
      $holiday_hours = $this->branchHoursHelper->getLazyBranchHolidayHours($node);
      $branch_hours = $this->branchHoursHelper->getLazyBranchHours($node);

      $request_time = $this->time->getRequestTime();
      $date = DrupalDateTime::createFromTimestamp($request_time, $this->branchHoursHelper->getTimezone());
      $today_weekday = $date->format('D');
      $today_date = $date->format('Y-m-d');

      if (!empty($holiday_hours[$today_date])) {
        $label = $holiday_hours[$today_date];
      }
      else if (!empty($branch_hours[$today_weekday])) {
        $label = $branch_hours[$today_weekday];
      }
    }

    return [
      '#markup' => '<div class="today">'. $label . '</div>',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['generateHoursToday'];
  }

}
