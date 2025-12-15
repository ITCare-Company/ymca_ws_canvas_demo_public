<?php

namespace Drupal\openy_xdt\EventSubscriber;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Openy Cross-domain Tracking (XDT) event subscriber.
 */
class OpenyXdtSubscriber implements EventSubscriberInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructor so we can read configuration.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Kernel response event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   Response event.
   */
  public function onKernelResponse(ResponseEvent $event) {
    // Grab a cookie to decorate the response url.
    if ($event->getResponse() instanceof TrustedRedirectResponse) {

      // Read in configuration.
      $config = $this->configFactory->get('openy_xdt.settings');
      $cookies = $config->get('cookies');
      $domains = $config->get('domains');

      // Get the destination url.
      /** @var \Drupal\Core\Routing\TrustedRedirectResponse $response */
      $response = $event->getResponse();
      $url = $response->getTargetUrl();

      // Decompose the URL into its parts as there may be existing queries.
      $parts = UrlHelper::parse($url);

      // If no domain is specified OR none match the url, do nothing.
      if (empty($domains) || !in_array(parse_url($parts['path'], PHP_URL_HOST), $domains)) {
        return;
      }

      // If the domain matches the url host then merge any additional cookies
      // into the query.
      foreach ($cookies as $cookie) {
        if (isset($_COOKIE[$cookie])) {
          $parts['query'][$cookie] = Xss::filter($_COOKIE[$cookie]);
        }
      }

      // Finally recompose the URL and convert it back to a string.
      $newUrl = Url::fromUri($parts['path'], [
        'query' => $parts['query'],
        'fragment' => $parts['fragment'],
      ])->toString();

      // Pass the new URL back to the response.
      $response->setTrustedTargetUrl($newUrl);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => ['onKernelResponse'],
    ];
  }

}
