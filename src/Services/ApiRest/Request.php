<?php
namespace Drupal\creneaux_shopify\Services\ApiRest;

// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Component\Serialization\Json;
use Stephane888\Debug\Utility as debugLogUtility;
use Drupal\ajax_status\Services\Status as AjaxStatus;

class Request {

  private $Request;

  protected $SwitchAction;

  public $AjaxStatus;

  function __construct(RequestStack $Request, SwitchAction $SwitchAction, AjaxStatus $AjaxStatus)
  {
    $this->Request = $Request->getCurrentRequest();
    $this->AjaxStatus = $AjaxStatus;
    $this->SwitchAction = $SwitchAction;
    $this->SwitchAction->AjaxStatus = $AjaxStatus;
  }

  public function request(string $action)
  {
    try {
      return $this->SwitchAction->getActions($action);
    } catch (\Exception $e) {
      $this->AjaxStatus->Codes->setCode(460);
      $this->AjaxStatus->Messages->setMessage($e->getMessage());
      return debugLogUtility::errorMessage($e);
    } catch (\Error $e) {
      $this->AjaxStatus->Codes->setCode(460);
      $this->AjaxStatus->Messages->setMessage($e->getMessage());
      return debugLogUtility::errorError($e);
    }
  }
}