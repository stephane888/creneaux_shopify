<?php
namespace Drupal\creneaux_shopify\Services\ApiRest;

class SwitchAction {

  public $AjaxStatus;

  protected $Ressources;

  function __construct(Ressources $Ressources)
  {
    $this->Ressources = $Ressources;
  }

  /**
   * Recupere l'action
   */
  function getActions($token)
  {
    $this->Ressources->AjaxStatus = $this->AjaxStatus;
    switch ($token) {
      case 'LoadMetafields':
        return $this->Ressources->LoadMetafields();
        break;
      case 'SaveMetafields':
        return $this->Ressources->SaveMetafields();
        break;
      default:
        $this->AjaxStatus->Codes->setCode(406);
        $this->AjaxStatus->Messages->setMessage(" Le token definit n'a pas de reference ou n'est plus traite : " . $token);
        break;
    }
  }
}