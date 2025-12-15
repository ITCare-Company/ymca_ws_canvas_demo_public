<?php

declare(strict_types=1);

namespace Drupal\daterange_compact;

final class Patterns {

  public function __construct(
    public string $startPattern,
    public string $endPattern,
    public string $separator,
  ) {
  }

}
