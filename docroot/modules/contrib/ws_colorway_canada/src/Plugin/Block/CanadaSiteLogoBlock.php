<?php


namespace Drupal\ws_colorway_canada\Plugin\Block;

use Drupal\y_lb\Plugin\Block\SiteLogoBlock;

class CanadaSiteLogoBlock extends SiteLogoBlock  {
  public function build() {
    $build = parent::build();
    $type_of_logo = $this->configuration['logo_type'];

    if ($type_of_logo !== 'theme') {
      $path = $this->moduleList
          ->getPath('ws_colorway_canada') . '/assets/svg/logo_' . $type_of_logo . '.svg';
      $logo_url = $this->fileUrlGenerator
        ->generateAbsoluteString($path);
      $build['#site_logo_is_svg'] = TRUE;
      $build['#site_logo_svg'] = file_get_contents($path);
      $build['#logo_url'] = $logo_url;
    }

    return $build;
  }


}

