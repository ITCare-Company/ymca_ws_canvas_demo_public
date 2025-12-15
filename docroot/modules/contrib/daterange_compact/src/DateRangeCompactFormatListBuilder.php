<?php

declare(strict_types=1);

namespace Drupal\daterange_compact;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of compact date range format entities.
 */
class DateRangeCompactFormatListBuilder extends ConfigEntityListBuilder {

  /**
   * The date range formatter service.
   */
  protected DateRangeCompactFormatterInterface $formatter;

  /**
   * Constructs a new DateRangeCompactFormatListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\daterange_compact\DateRangeCompactFormatterInterface $formatter
   *   The date formatter service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateRangeCompactFormatterInterface $formatter) {
    parent::__construct($entity_type, $storage);
    $this->formatter = $formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('daterange_compact.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['label'] = $this->t('Name');
    $header['datetime'] = $this->t('Examples');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface $format */
    $format = $entity;

    $row['label'] = $format->label();
    $row['datetime']['data'] = $this->examples($format);
    return $row + parent::buildRow($entity);
  }

  /**
   * Examples of various datetime ranges shown using the given format.
   *
   * @param \Drupal\daterange_compact\Entity\DateRangeCompactFormatInterface $format
   *   The date range format entity.
   *
   * @return array
   *   A render array suitable for use within the list builder table.
   */
  protected function examples(DateRangeCompactFormatInterface $format): array {
    $examples = [];

    // An example range that is a single date and time.
    $same_time_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-01 09:00')->getTimestamp();
    $examples[] = $this->formatter->formatTimestampRange(
      $same_time_timestamp, $same_time_timestamp, $format->id());

    // An example range that is contained within a single day.
    if ($format->get('same_day_start_pattern') && $format->get('same_day_end_pattern')) {
      $same_day_start_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-01 09:00')->getTimestamp();
      $same_day_end_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-01 10:30')->getTimestamp();
      $examples[] = $this->formatter->formatTimestampRange(
        $same_day_start_timestamp, $same_day_end_timestamp, $format->id());
    }

    // An example range that spans several days within the same month.
    if ($format->get('same_month_start_pattern') && $format->get('same_month_end_pattern')) {
      $same_month_start_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-02 09:00')->getTimestamp();
      $same_month_end_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-03 10:00')->getTimestamp();
      $examples[] = $this->formatter->formatTimestampRange(
        $same_month_start_timestamp, $same_month_end_timestamp, $format->id());
    }

    // An example range that spans several months within the same year.
    if ($format->get('same_year_start_pattern') && $format->get('same_year_end_pattern')) {
      $same_year_start_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-04 09:00')->getTimestamp();
      $same_year_end_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-02-05 10:00')->getTimestamp();
      $examples[] = $this->formatter->formatTimestampRange(
        $same_year_start_timestamp, $same_year_end_timestamp, $format->id());
    }

    // An example range that spans multiple years.
    $multi_year_start_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-06 09:00')->getTimestamp();
    $multi_year_end_timestamp = \DateTime::createFromFormat('Y-m-d H:i', '2018-01-07 10:00')->getTimestamp();
    $examples[] = $this->formatter->formatTimestampRange(
      $multi_year_start_timestamp, $multi_year_end_timestamp, $format->id());

    $output = '';
    foreach ($examples as $example) {
      $output .= htmlspecialchars($example->text) . '<br>';
    }
    return ['#markup' => $output];
  }

}
