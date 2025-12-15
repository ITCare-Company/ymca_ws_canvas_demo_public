<?php

namespace Drupal\small_y_editor\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

const TABLE_START_FILTER_REGEX = '/\<table.*?\>/s';
const TABLE_END_FILTER_REGEX = '/\<\/table.*?\>/s';


/**
 * Add Bootstrap Class '.table' to any tables.
 *
 * @Filter(
 *   id = "small_y_table_filter",
 *   title = @Translation("Add Bootstrap '.table' class to any tables"),
 *   description = @Translation("This filter will add class '.table' to any tables."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class TableBootstrapFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $text = preg_replace_callback(TABLE_START_FILTER_REGEX, [
      $this,
      'tableBootstrapsFilterReplace',
    ], $text);

    $text = preg_replace(TABLE_END_FILTER_REGEX, '</table></div>', $text);
    return new FilterProcessResult($text);
  }

  /**
   * Callback to convert a table to bootstrap table.
   *
   * @param string $match
   *   Takes a match of tag code.
   *
   * @return string
   *   The HTML markup representation of the tag, or an empty string on failure.
   */
  private function tableBootstrapsFilterReplace($match) {
    $table_id = '';
    $table_classes = 'table';
    $table_styles = '';

    if (preg_match('/id="(.+)"/', $match[0], $id)) {
      $table_id = ' id="' . $id[1] . '"';
    }
    if (preg_match('/class="(.+)"/U', $match[0], $classes)) {
      $table_classes .= ' ' . $classes[1];
    }
    if (preg_match('/style="(.+)"/', $match[0], $styles)) {
      $table_styles = 'style="' . $styles[1] . '"';
      if ($this->settings['remove_width_height']) {
        $table_styles = preg_replace('/width.+;/', '', $table_styles);
      }
    }

    return '<div class="table-responsive"><table class="' . ltrim($table_classes) . '"' . $table_id . $table_styles . '>';
  }

}
