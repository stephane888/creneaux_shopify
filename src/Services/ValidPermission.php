<?php
namespace Drupal\creneaux_shopify\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Pizdata\OAuth2\Client\Provider\Shopify;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Stephane888\Debug\debugLog;
use Drupal\Core\Url;

class ValidPermission {

  protected $request;

  protected $ShopifyHost;

  private $configs;

  private $modeDebug = true;

  function __construct(RequestStack $RequestStack, ShopifyHost $ShopifyHost)
  {
    $this->request = $RequestStack->getCurrentRequest();
    $this->ShopifyHost = $ShopifyHost;
    $this->configs = \Drupal::config('creneaux_shopify.settings');
  }

  /**
   * Demande de permissions ou d'acces au compte client.
   */
  public function getPermissions()
  {
    $config = [
      'clientId' => $this->configs->get('key_app'), // The client ID assigned to you by the Shopify
      'clientSecret' => $this->configs->get('secret_app'), // The client password assigned to you by the Shopify
      'redirectUri' => $this->configs->get('redirect_uri'), // The redirect URI assigned to you
      'shop' => $this->request->query->get('shop', 'kksa.shopify.com') // The Shop name
    ];
    $provider = new Shopify($config);
    /**
     * Demande d'autorisation.
     */
    if (! $this->request->query->get('code')) {
      // Setting up scope
      /*
       * $options = [
       * 'scope' => [
       * 'read_content',
       * 'write_content',
       * 'read_themes',
       * 'write_themes',
       * 'read_products',
       * 'write_products',
       * 'read_customers',
       * 'write_customers',
       * 'read_orders',
       * 'write_orders',
       * 'read_draft_orders',
       * 'write_draft_orders',
       * 'read_script_tags',
       * 'write_script_tags',
       * 'read_fulfillments',
       * 'write_fulfillments',
       * 'read_shipping',
       * 'write_shipping',
       * 'read_analytics'
       * ]
       * ];
       * /*
       */
      $options = [
        'scope' => [
          'read_themes',
          'write_themes'
        ]
      ];
      // Fetch the authorization URL from the provider; this returns the
      // urlAuthorize option and generates and applies any necessary parameters
      // (e.g. state).
      $authorizationUrl = $provider->getAuthorizationUrl($options);

      // Get the state generated for you and store it to the session.
      $_SESSION['oauth2state'] = $provider->getState();

      /**
       * Deboggage lors de la premiere requete.
       */
      $debug = [
        'authorizationUrl' => $authorizationUrl,
        'config' => $config,
        '_GET' => $this->request->query->keys()
      ];
      $this->debug($debug, 'debug-demande-installation');

      // Redirect the user to the authorization URL.
      header('Location: ' . $authorizationUrl);
      exit();
    } elseif (empty($this->request->query->get('state')) || (isset($_SESSION['oauth2state']) && $this->request->query->get('state') !== $_SESSION['oauth2state'])) {
      $debug = [
        'oauth2state' => $_SESSION['oauth2state'],
        'state' => $this->request->query->get('state')
      ];
      if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
      }
      $this->debug($debug, 'debug-echec-installation');
      exit('Invalid state');
    } else {
      try {
        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
          'code' => $_GET['code']
        ]);

        $store = $provider->getResourceOwner($accessToken);

        // Access to Store base information
        $host = [
          'name' => $store->getName(),
          'email' => $store->getEmail(),
          'domain' => $store->getDomain(),
          'all' => $store->toArray(),
          'token' => $accessToken->getToken()
        ];
        $this->debug($host, 'debug-permission');
        $this->ShopifyHost->ManageHost($host);
        // on regirige vers la page de l'application;
        return $this->redirection($host);
      } catch (IdentityProviderException $e) {
        // Failed to get the access token or user details.
        exit($e->getMessage());
        return null;
      }
    }
  }

  protected function redirection($host, $route_name = 'creneaux_shopify.configs')
  {
    $options['absolute'] = TRUE;
    $options['query']['token'] = $host['token'];
    foreach ($this->request->query->keys() as $param) {
      $options['query'][$param] = $this->request->query->get($param);
    }
    $route_parameters = [];
    $status = 302;
    $urlObject = Url::fromRoute($route_name, $route_parameters, $options)->toString();
    return new RedirectResponse($urlObject, $status);
  }

  private function debug($datas, $name)
  {
    if ($this->modeDebug)
      debugLog::SaveLogsDrupal($datas, $name);
  }
}