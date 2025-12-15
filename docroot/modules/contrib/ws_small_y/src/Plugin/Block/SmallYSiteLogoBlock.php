<?php

namespace Drupal\ws_small_y\Plugin\Block;

use Drupal\y_lb\Plugin\Block\SiteLogoBlock;

/**
 * Override the Site logo block.
 */
class SmallYSiteLogoBlock extends SiteLogoBlock {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    $type_of_logo = $this->configuration['logo_type'];

    if ($type_of_logo !== 'theme') {
      $path = $this->moduleList
          ->getPath('ws_small_y') . '/assets/svg/logo_' . $type_of_logo . '.svg';
      $logo_url = $this->fileUrlGenerator
        ->generateAbsoluteString($path);
      $build['#site_logo_is_svg'] = TRUE;
      $build['#site_logo_svg'] = file_get_contents($path);
      $build['#logo_url'] = $logo_url;
    }

    return $build;
  }


}

