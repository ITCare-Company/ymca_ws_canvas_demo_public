# YMCA Website Services Donation Embed Form

The YMCA Donate module provides templated donation forms enabling YMCAs
to add a donation form to their site with minimal code.

## Requirements

This module requires no modules outside of Drupal core.

## Recommended modules

Y Layout Builder - Donate: A submodule of this module, this will allow editors
to create a call to action block in Layout Builder
that directs users to the embedded donation form.

## Installation

Install as you would normally install a contributed Drupal module.
For further information, see [Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## Configuration

1. Enable the module at Administration > Extend.
2. Select the **Layout** Tab of a Layout Builder-enabled page.
3. Select **Add block** on the page,
   then search or scroll to find **Donation Form Embed Block**.
4. Select the form type and enter the form ID from your donation provider.

## Troubleshooting

If your embedded form does not work in your non-production environment
you may need to add a domain to the allow-list either on the provider-side
or in your site's Content Security Policy.

## FAQ

**Q: What if my donation provider isn't listed?**

**A1:** If your provider is not listed you can add the form by selecting
the **Code** Custom Block and then pasting in your code.

## Information for Developers

To add a new donation provider:

1. Extend this method to add the new provider:
   `\Drupal\y_donate\Plugin\Block\DonateFormBlock::listFormProviders`.
2. Create a twig template that matches the key of the new provider.
3. Add any required external js via a new library.
