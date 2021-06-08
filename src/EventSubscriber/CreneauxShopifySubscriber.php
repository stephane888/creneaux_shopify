<?php
namespace Drupal\creneaux_shopify\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Creneaux Shopify event subscriber.
 */
class CreneauxShopifySubscriber implements EventSubscriberInterface {

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *          The messenger.
   */
  public function __construct(MessengerInterface $messenger)
  {
    $this->messenger = $messenger;
  }

  /**
   * Kernel request event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *          Response event.
   */
  public function onKernelRequest(GetResponseEvent $event)
  {
    $this->messenger->addStatus(__FUNCTION__);
  }

  /**
   * Kernel response event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *          Response event.
   */
  public function onKernelResponse(FilterResponseEvent $event)
  {
    $this->messenger->addStatus(__FUNCTION__);
  }

  public function setHeaderContentSecurityPolicy2(FilterResponseEvent $event)
  {
    $referer = $event->getRequest()->headers->get('referer');
    if (strpos($referer, 'https://www.example.com/') === 0) {
      $response = $event->getResponse();
      $response->headers->remove('X-Frame-Options');
      $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' example.com *.example.com", FALSE);
    }
  }

  /**
   * Set header 'Content-Security-Policy' to response to allow embedding in iFrame.
   */
  public function setHeaderContentSecurityPolicy(FilterResponseEvent $event)
  {
    // dump($event->getRequest()->headers->keys());
    $response = $event->getResponse();
    $response->headers->remove('X-Frame-Options');
    $response->headers->set('Content-Security-Policy', "default-src 'self' 'unsafe-eval' 'unsafe-inline' *.bootstrapcdn.com cdn.jsdelivr.net code.jquery.com cdnjs.cloudflare.com www.w3.org; frame-ancestors 'self' *.myshopify.com;");
  }

  /**
   *
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    return [
      KernelEvents::REQUEST => [
        'onKernelRequest'
      ],
      KernelEvents::RESPONSE => [
        'onKernelResponse'
      ],
      KernelEvents::RESPONSE => [
        'setHeaderContentSecurityPolicy',
        - 10
      ]
    ];
  }
}
