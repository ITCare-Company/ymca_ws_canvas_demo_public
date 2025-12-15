# YMCA Website Services Cross-domain Tracking (XDT)

This configuration enables cross-domain tracking to work through internal redirects (that use `TrustedRedirectResponse`) in addition to regular links.

When enabled, cookies matching any configured tag will be added to any redirect destination matching a configured domain. For example, a redirect to `https://example.com` will be transformed to `https://example.com/?_gl=....`

For a full description of the module, visit the [documentation](https://ds-docs.y.org/docs/howto/track-users/#cross-domain-tracking).

## Requirements

This project is meant to be used with the [YMCA's Website Service distribution](https://www.drupal.org/project/openy).

Configuration and testing of analytics is required outside the scope of this module, refer to [[GA4] Set up cross-domain measurement](https://support.google.com/analytics/answer/10071811?hl=en) for more information.

Successful cross-domain tracking also requires the destination application to retain the passed query strings and load it into the corresponding tracking property.

## Installation

Install as you would normally install a contributed Drupal module. For further information, see [Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## Configuration

1. Enable the module at Administration > Extend, or via drush:
    ```shell
    drush en openy_xdt
    ```
2. Configure the module at Administration > YMCA Website Services > Settings > Cross-domain Tracking Settings (`/admin/openy/settings/xdt`)
   - The cookie defaults to the standard for GA4, but can be modified for use with different systems.
   - The module will not have any effect until a domain is configured. Add the domains of any external sites where you would like to enable tracking.
