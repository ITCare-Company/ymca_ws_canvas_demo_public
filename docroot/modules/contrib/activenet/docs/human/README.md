# ActiveNet Integration Module

This module provides a basic integration with the ActiveNet API for Drupal sites.

## Project Purpose

The primary goal of this module is to connect your Drupal site with the ActiveNet system, allowing Drupal to retrieve data via the ActiveNet API.

## Key Features

The integration provides the ability to retrieve various types of data from ActiveNet. Based on the available client methods, the module can fetch:

*   Sites
*   Centers
*   Activities
*   Activity Types
*   Activity Categories
*   Activity Other Categories
*   FlexReg Programs
*   FlexReg Program Types
*   Membership Packages
*   Membership Categories
*   Details for a specific Activity by ID

## Installation and Setup

1.  **Enable the module:** Enable the ActiveNet module within your Drupal installation.
2.  **Configure settings:** After enabling, navigate to the module's settings form to input your ActiveNet credentials.
    *   Go to `/admin/openy/integrations/activenet/settings`.
    *   Enter the required ActiveNet API Base URI and API Key.
3.  Ensure the base URI format is correct (e.g., `https://{host address}/{service name}/{organization id}/api/{API version}/`).
4.  Ensure the API key is the long string provided by ActiveNet support.

## Basic Usage

Once the module is enabled and configured, you can interact with the ActiveNet API using the provided client service.

You can access the ActiveNet client service using Drupal's service container:

```php
use Drupal\activenet\ActivenetClientInterface;

// Get the ActiveNet client service.
/** @var \Drupal\activenet\ActivenetClientInterface $client */
$client = \Drupal::service('activenet.client');

// Example: Get all centers.
try {
  $centers = $client->getCenters();
  print_r($centers); // This will print the data returned by the API.
}
catch (\Drupal\activenet\ActivenetClientException $e) {
  // Handle potential API errors.
  \Drupal::logger('activenet')->error('Error fetching centers: @message', ['@message' => $e->getMessage()]);
}
```

You can call any of the available `get*` methods on the `$client` object, such as:

*   `$client->getSites()`
*   `$client->getActivities()`
*   `$client->getActivityTypes()`
*   `$client->getActivityCategories()`
*   `$client->getActivityOtherCategories()`
*   `$client->getFlexRegPrograms()`
*   `$client->getFlexRegProgramTypes()`
*   `$client->getMembershipPackages()`
*   `$client->getMembershipCategories()`
*   `$client->getActivityDetail($id)` (replace `$id` with the activity's numeric ID)

Each method is expected to return the parsed JSON response data from the corresponding ActiveNet API endpoint. API errors or communication issues will result in an `ActivenetClientException` being thrown.