<?php
namespace Drupal\creneaux_shopify\Services\ApiRest;

use Symfony\Component\HttpFoundation\RequestStack;
// use Symfony\Component\HttpFoundation\Session\Session;
use Stephane888\Debug\Session;
use Wbu\ApiRest\Metafields\MetafieldsToken;
use Drupal\Component\Serialization\Json;
use Stephane888\Debug\debugLog;

class Ressources {

  private $Request;

  private $requetContent;

  private $modeDebug = false;

  protected $SwitchAction;

  public $AjaxStatus;

  protected $namespace = "wbu_creneaux";

  protected $configs;

  protected $endPoint;

  function __construct(RequestStack $Request)
  {
    $this->Request = $Request->getCurrentRequest();
    $this->setconfigs();
    $this->getRequestRessources();
  }

  function LoadMetafields()
  {
    $MetafieldsToken = new MetafieldsToken($this->configs, $this->namespace);
    $MetafieldsToken->requestEndPoint = $this->endPoint;
    $Metafields = $MetafieldsToken->LoadMetafiels();
    if ($MetafieldsToken->hasError()) {
      $this->AjaxStatus->Codes->setCode(460);
      $this->AjaxStatus->Messages->setMessage('Error lors du chargement du metafield');
      return $Metafields;
    } else {
      $this->AjaxStatus->Codes->setCode(200);
      $this->AjaxStatus->Messages->setMessage('Chargement des données avec success');
      return $Metafields;
    }
  }

  /**
   * //
   */
  function SaveMetafields()
  {
    if (! empty($this->requetContent['metafields'])) {
      $MetafieldsToken = new MetafieldsToken($this->configs, $this->namespace);
      $MetafieldsToken->requestEndPoint = $this->endPoint;
      $MetafieldsToken->default_ressource = true;
      $Metafields = $MetafieldsToken->save($this->requetContent['metafields']);
      if ($MetafieldsToken->hasError()) {
        $this->AjaxStatus->Codes->setCode(460);
        $this->AjaxStatus->Messages->setMessage("le metafield n'a pas pu etre enregistrer sur shopify");
        return $Metafields;
      } else {
        return $Metafields;
      }
    } else {
      $this->AjaxStatus->Codes->setCode(460);
      $this->AjaxStatus->Messages->setMessage('le metafield est vide ou non definit');
      return $this->requetContent;
    }
  }

  private function setconfigs()
  {
    $this->configs = [
      'token' => $this->Request->query->get('token'),
      'domaine' => $this->Request->query->get('shop')
    ];
    $debug = [
      'configs' => $this->configs,
      'user' => \Drupal::currentUser()->isAuthenticated(),
      'user_id' => \Drupal::currentUser()->id(),
      '$_GET' => $_GET
    ];
    $this->debug($debug, 'Ressources::setconfigs');
  }

  /**
   * Recupere les données utiles pour effectuer la requete.
   */
  private function getRequestRessources()
  {
    $content = $this->requetContent = Json::decode($this->Request->getContent());
    if (isset($content['endPoint'])) {
      $this->endPoint = $content['endPoint'];
    }
    $this->debug($content, 'Ressources::getRequestRessources');
  }

  private function debug($datas, $name)
  {
    if ($this->modeDebug)
      debugLog::SaveLogsDrupal($datas, $name);
  }
}