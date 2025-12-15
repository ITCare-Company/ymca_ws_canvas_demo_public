<?php

/**
 * @file
 * Custom tokens for ws events.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\node\NodeInterface;

/**
 * Implements hook_token_info().
 */
function ws_event_token_info() {
  $node['combined-address'] = [
    'name' => t("Combined address"),
    'description' => t("The full street address from an Event address field if it exists, otherwise from a referenced location address."),
  ];
  $node['combined-address-name'] = [
    'name' => t("Combined address name"),
    'description' => t("The name of the combined address."),
  ];
  $node['combined-address-url'] = [
    'name' => t("Combined address url"),
    'description' => t("The url of the combined address."),
  ];
  $node['combined-address-street-address'] = [
    'name' => t("Combined address street address"),
    'description' => t("The street address of the combined address."),
  ];
  $node['combined-address-locality'] = [
    'name' => t("Combined address city/locality"),
    'description' => t("The city or locality of the combined address."),
  ];
  $node['combined-address-region'] = [
    'name' => t("Combined address state/region"),
    'description' => t("The stage or region of the combined address."),
  ];
  $node['combined-address-postal-code'] = [
    'name' => t("Combined address postal code"),
    'description' => t("The postal code of the combined address."),
  ];
  $node['combined-address-country'] = [
    'name' => t("Combined address country"),
    'description' => t("The country of the combined address."),
  ];

  return [
    'tokens' => [
      'node' => $node,
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function ws_event_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type !== 'node' || empty($data['node'])) {
    return $replacements;
  }

  /** @var NodeInterface $node */
  $node = $data['node'];

  foreach ($tokens as $name => $original) {
    if (!str_contains($name, 'combined-address')) {
      continue;
    }

    $locTitle = '';
    $address_array = [];
    $locationUrl = '';

    if ($node->hasField('field_location_address') && !$node->get('field_location_address')->isEmpty()) {
      $firstAddress = $node->get('field_location_address')->first();
      $locTitle = $firstAddress?->organization ?? '';
      $address_array = $firstAddress?->toArray() ?? [];
    }
    elseif ($node->hasField('field_location_reference') && !$node->get('field_location_reference')->isEmpty()) {
      $location_id = $node->get('field_location_reference')->first()?->get('target_id')->getValue() ?? null;
      $location = $location_id ? \Drupal::entityTypeManager()->getStorage('node')->load($location_id) : null;

      if ($location instanceof NodeInterface) {
        $locTitle = $location->getTitle() ?? '';
        $firstAddress = $location->get('field_location_address')->first();
        $address_array = $firstAddress?->toArray() ?? [];
        $locationUrl = $location->toUrl()->setAbsolute()->toString();
      }
    }

    switch ($name) {
      case 'combined-address':
        $locAddress = implode(', ', array_filter([
          $address_array['address_line1'] ?? '',
          $address_array['locality'] ?? '',
          $address_array['administrative_area'] ?? '',
          $address_array['postal_code'] ?? ''
        ]));
        $replacements[$original] = implode(', ', array_filter([$locTitle, $locAddress]));
        break;

      case 'combined-address-name':
        $replacements[$original] = $locTitle;
        break;

      case 'combined-address-url':
        $replacements[$original] = $locationUrl;
        break;

      case 'combined-address-street-address':
        $replacements[$original] = $address_array['address_line1'] ?? '';
        break;

      case 'combined-address-locality':
        $replacements[$original] = $address_array['locality'] ?? '';
        break;

      case 'combined-address-region':
        $replacements[$original] = $address_array['administrative_area'] ?? '';
        break;

      case 'combined-address-postal-code':
        $replacements[$original] = $address_array['postal_code'] ?? '';
        break;

      case 'combined-address-country':
        $replacements[$original] = $address_array['country_code'] ?? '';
        break;
    }
  }

  return $replacements;
}
