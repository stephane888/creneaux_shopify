<?php
namespace Drupal\creneaux_shopify\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\ajax_status\Services\Status as AjaxStatus;
use Drupal\Core\Database\Connection;

class CreneauxTest  {

  /**
   * Pour le moment on stocke cela ici, on va modifier la table apres avoir trouver un mecanisme pour gerer les utilisateurs.
   *
   * @var string
   */
  const table_creneaux_shopify_token = "creneaux_shopify_token";

  private $Request;

  protected $Connection;

  public $AjaxStatus;

  function __construct(RequestStack $Request, Connection $Connection, AjaxStatus $AjaxStatus)
  {
    $this->Request = $Request->getCurrentRequest();
    $this->AjaxStatus = $AjaxStatus;
    $this->Connection = $Connection;
  }

  function saveCreneauxTests()
  {
    try {
      $token = $this->Request->query->get('token');
      $store = $this->getStoreByToken($token);
      if ($store) {
        $body = $this->Request->getContent();
        $fields = [
          'creneaux_configs' => $body,
          'id' => $store['id']
        ];
        $this->save($fields, self::table_creneaux_shopify_token);
        $this->AjaxStatus->Codes->setCode(200);
        $this->AjaxStatus->Messages->setMessage("Sauvegarde test ok ");
      } else {
        $this->AjaxStatus->Codes->setCode(400);
        $this->AjaxStatus->Messages->setMessage("La boutique n'existe pas.");
      }
      return 'add';
    } catch (\Exception $e) {
      $this->AjaxStatus->Codes->setCode(500);
      $this->AjaxStatus->Messages->setMessage($e->getMessage());
      return $e->getTrace();
    }
  }

  public function save($fields, $table)
  {
    if (isset($fields['id'])) {
      return $this->Connection->update($table)
        ->fields($fields)
        ->condition('id', $fields['id'], '=')
        ->execute();
      ;
    } else {
      return $this->Connection->insert($table)
        ->fields($fields)
        ->execute();
    }
  }

  public function loadCurrentConfig()
  {
    $token = $this->Request->query->get('token');
    return $this->getStoreByToken($token);
  }

  protected function getStoreByToken(string $token)
  {
    $query = $this->Connection->select(self::table_creneaux_shopify_token, 'h')->fields('h');
    $query->condition('token', $token);
    return $query->execute()->fetch(\PDO::FETCH_ASSOC);
  }
}