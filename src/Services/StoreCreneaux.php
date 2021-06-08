<?php
namespace Drupal\creneaux_shopify\Services;

use Wbu\ApiRest\Metafields\MetafieldsToken;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 *
 * @author stephane
 * @deprecated ce fichier n'est plus utilisé.
 */
class StoreCreneaux {

  protected $namespace = "wbu_creneaux";

  protected $configs;

  private function setconfigs()
  {
    $Session = new Session();
    $this->configs = [
      'token' => $Session->get('creneau-shopify-token'),
      'domaine' => $Session->get('creneau-shopify-domaine')
    ];
  }

  function getShopMetafields()
  {
    $this->setconfigs();
    $MetafieldsToken = new MetafieldsToken($this->configs, $this->namespace);
    $MetafieldsToken->requestEndPoint = '/admin/metafields.json';
    $Metafields = $MetafieldsToken->LoadMetafiels();
    if ($MetafieldsToken->hasError()) {
      return $Metafields;
    } else {
      return $Metafields;
    }
  }

  function saveCreneaux()
  {
    $this->setconfigs();
    $MetafieldsToken = new MetafieldsToken($this->configs, $this->namespace);
    $MetafieldsToken->requestEndPoint = '/admin/metafields.json';
  }
}