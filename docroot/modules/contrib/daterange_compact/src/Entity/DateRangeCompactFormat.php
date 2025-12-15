<?php

declare(strict_types=1);

namespace Drupal\daterange_compact\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\daterange_compact\Patterns;

/**
 * Defines the compact date range format entity.
 *
 * @ConfigEntityType(
 *   id = "daterange_compact_format",
 *   label = @Translation("Compact date range format"),
 *   handlers = {
 *     "list_builder" = "Drupal\daterange_compact\DateRangeCompactFormatListBuilder",
 *     "form" = {
 *       "add" = "Drupal\daterange_compact\Form\DateRangeCompactFormatForm",
 *       "edit" = "Drupal\daterange_compact\Form\DateRangeCompactFormatForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "format",
 *   admin_permission = "administer site configuration",
 *   list_cache_tags = { "rendered" },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/regional/daterange-compact-format/{daterange_compact_format}",
 *     "add-form" = "/admin/config/regional/daterange-compact-format/add",
 *     "edit-form" = "/admin/config/regional/daterange-compact-format/{daterange_compact_format}/edit",
 *     "delete-form" = "/admin/config/regional/daterange-compact-format/{daterange_compact_format}/delete",
 *     "collection" = "/admin/config/regional/daterange-compact-format"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "default_pattern",
 *     "default_separator",
 *     "same_day_start_pattern",
 *     "same_day_end_pattern",
 *     "same_day_separator",
 *     "same_day_omit_duplicate_ampm",
 *     "same_month_start_pattern",
 *     "same_month_end_pattern",
 *     "same_month_separator",
 *     "same_year_start_pattern",
 *     "same_year_end_pattern",
 *     "same_year_separator",
 *     "zero_minutes_omit",
 *     "zero_minutes_omit_pattern",
 *   }
 * )
 */
class DateRangeCompactFormat extends ConfigEntityBase implements DateRangeCompactFormatInterface {

  /**
   * The ID of this format.
   */
  protected string $id;

  /**
   * The human-readable name of this format.
   */
  protected string $label;

  public bool $zero_minutes_omit = FALSE;

  public string $zero_minutes_omit_pattern = ':i';

  public bool $same_day_omit_duplicate_ampm = FALSE;

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate(): array {
    return ['rendered'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultPatterns(): Patterns {
    return new Patterns(
      $this->get('default_pattern'),
      $this->get('default_pattern'),
      $this->get('default_separator'),
    );
  }

//  /**
//   * {@inheritdoc}
//   */
//  public function getSameHalfDayPatterns(): ?Patterns {
//    $start_pattern = $this->get('same_day_start_pattern') ?: '';
//    $end_pattern = $this->get('same_day_end_pattern') ?: '';
//    $separator = $this->get('same_day_separator') ?: $this->get('default_separator');
//    if ($start_pattern || $end_pattern) {
//      if ($this->same_day_omit_duplicate_ampm) {
//        // Use a negative look behind to match instances of 'a' that do
//        // not immediately follow a backslash:
//        $pattern = '/(?<!\\\)a/';
//        $start_pattern = preg_replace($pattern, '', $start_pattern);
//      }
//      return new Patterns(
//        $start_pattern,
//        $end_pattern,
//        ($start_pattern && $end_pattern) ? $separator : ''
//      );
//    }
//    else {
//      return NULL;
//    }
//  }

  /**
   * {@inheritdoc}
   */
  public function getSameDayPatterns(): ?Patterns {
    $start_pattern = $this->get('same_day_start_pattern') ?: '';
    $end_pattern = $this->get('same_day_end_pattern') ?: '';
    $separator = $this->get('same_day_separator') ?: $this->get('default_separator');
    if ($start_pattern || $end_pattern) {
      return new Patterns(
        $start_pattern,
        $end_pattern,
        ($start_pattern && $end_pattern) ? $separator : ''
      );
    }
    else {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSameMonthPatterns(): ?Patterns {
    $start_pattern = $this->get('same_month_start_pattern') ?: '';
    $end_pattern = $this->get('same_month_end_pattern') ?: '';
    $separator = $this->get('same_month_separator') ?: $this->get('default_separator');
    if ($start_pattern || $end_pattern) {
      return new Patterns(
        $start_pattern,
        $end_pattern,
        ($start_pattern && $end_pattern) ? $separator : '',
      );
    }
    else {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getSameYearPatterns(): ?Patterns {
    $start_pattern = $this->get('same_year_start_pattern') ?: '';
    $end_pattern = $this->get('same_year_end_pattern') ?: '';
    $separator = $this->get('same_year_separator') ?: $this->get('default_separator');
    if ($start_pattern || $end_pattern) {
      return new Patterns(
        $start_pattern,
        $end_pattern,
        ($start_pattern && $end_pattern) ? $separator : ''
      );
    }
    else {
      return NULL;
    }
  }

}
