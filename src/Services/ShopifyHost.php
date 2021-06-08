<?php
namespace Drupal\creneaux_shopify\Services;

use Drupal\Core\Database\Connection;
use Stephane888\Debug\debugLog;
use Stephane888\Debug\Session;
use Drupal\Component\Utility\Random;
use Drupal\user\Entity\User;

class ShopifyHost {

  private $modeDebug = false;

  protected $Connection;

  const table_creneaux_shopify_host = "creneaux_shopify_host";

  const table_creneaux_shopify_token = "creneaux_shopify_token";

  const table_creneaux_shopify_host_infos = "creneaux_shopify_host_infos";

  function __construct(Connection $Connection)
  {
    $this->Connection = $Connection;
  }

  /**
   * Charge.
   */
  public function ManageHost(array $host)
  {
    $currentHost = $this->loadShop($host['domain']);
    if (! $currentHost) {
      $fields = [
        'name' => $host['name'],
        'email' => $host['email'],
        'domaine' => $host['domain'],
        'status' => 1
      ];
      $id_host = $this->save($fields, self::table_creneaux_shopify_host);
      $fields = [
        'id_host' => $id_host,
        'token' => $host['token']
      ];
      $this->save($fields, self::table_creneaux_shopify_token);
      $fields = [
        'id_host' => $id_host,
        'created_at' => isset($host['all']['created_at']) ? strtotime($host['all']['created_at']) : null,
        'updated_at' => isset($host['all']['updated_at']) ? strtotime($host['all']['created_at']) : null,
        'country' => isset($host['all']['country']) ? $host['all']['country'] : null,
        'plan_name' => isset($host['all']['plan_name']) ? $host['all']['plan_name'] : null,
        'password_enabled' => ! empty($host['all']['password_enabled']) ? $host['all']['password_enabled'] : 0
      ];
      $this->save($fields, self::table_creneaux_shopify_host_infos);
    }
    // $Session = new Session();
    // $Session->set('creneau-shopify-domaine', $host['domain']);
    /* */
    // $this->ActiveAssociateUser($host);
  }

  function ActiveAssociateUser(array $host)
  {
    $userExist = user_load_by_name($host['domain']);
    if ($userExist) {
      user_login_finalize($userExist);
    } else {
      $this->addUser($host['domain'], $host['email']);
    }
  }

  /**
   * Charge une boutique s'il est existe (on identifie une boutique par le domaine.)
   *
   * @param String $domain
   */
  public function loadShop(String $domain)
  {
    $query = $this->Connection->select(self::table_creneaux_shopify_host, 'h')->fields('h');
    $query->condition('domaine', $domain);
    return $query->execute()->fetch(\PDO::FETCH_ASSOC);
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

  /**
   * cree la table de configuration pour magento.
   */
  public function table()
  {
    $table = self::table_creneaux_shopify_host;
    $schema = array(
      'description' => 'The base table for inactive_users.',
      'fields' => array(
        'id_host' => array(
          'description' => '.',
          'type' => 'serial', // Use 'serial' for auto incrementing fields.
          'unsigned' => TRUE,
          'not null' => TRUE
        ),
        'name' => array(
          'type' => 'varchar',
          'length' => 100,
          'not null' => false
        ),
        'email' => array(
          'type' => 'varchar',
          'length' => 100,
          'not null' => false
        ),
        'domaine' => array(
          'type' => 'varchar',
          'length' => 100,
          'not null' => false
        ),
        'status' => array(
          'type' => 'int',
          'size' => 'tiny',
          'not null' => false
        )
      ),
      'primary key' => array(
        'id_host'
      ),
      'unique keys' => [
        'key_domaine' => [
          'domaine'
        ]
      ]
    );
    /**
     * sauvagerde les tokens.
     */
    $this->create($table, $schema);
    $table = self::table_creneaux_shopify_token;
    $schema = array(
      'description' => 'The base table for inactive_users.',
      'fields' => array(
        'id' => array(
          'description' => '.',
          'type' => 'serial', // Use 'serial' for auto incrementing fields.
          'unsigned' => TRUE,
          'not null' => TRUE
        ),
        'id_host' => array(
          'type' => 'int',
          'size' => 'tiny',
          'not null' => TRUE
        ),
        'token' => array(
          'type' => 'varchar',
          'length' => 200,
          'not null' => TRUE
        ),
        'create_at' => array(
          'type' => 'int',
          'size' => 'tiny',
          'not null' => false
        ),
        'expired_at' => array(
          'type' => 'int',
          'size' => 'tiny',
          'not null' => false
        )
      ),
      'primary key' => array(
        'id'
      )
    );
    $this->create($table, $schema);
    /**
     * sauvagerde les infos sur la boutique.
     */
    $this->create($table, $schema);
    $table = self::table_creneaux_shopify_host_infos;
    $schema = array(
      'description' => 'The base table for inactive_users.',
      'fields' => array(
        'id' => array(
          'description' => '.',
          'type' => 'serial', // Use 'serial' for auto incrementing fields.
          'unsigned' => TRUE,
          'not null' => TRUE
        ),
        'id_host' => array(
          'type' => 'int',
          'size' => 'normal',
          'not null' => True
        ),
        'created_at' => array(
          'type' => 'int',
          'size' => 'normal',
          'not null' => false
        ),
        'updated_at' => array(
          'type' => 'int',
          'size' => 'normal',
          'not null' => false
        ),
        'country' => array(
          'type' => 'varchar',
          'length' => 200,
          'not null' => false
        ),
        'plan_name' => array(
          'type' => 'varchar',
          'length' => 200,
          'not null' => false
        ),
        'password_enabled' => array(
          'type' => 'int',
          'size' => 'tiny',
          'not null' => false
        )
      ),
      'primary key' => array(
        'id'
      )
    );
    $this->create($table, $schema);
  }

  private function create($table, $schema)
  {
    if (! $this->Connection->schema()->tableExists($table)) {
      $this->Connection->schema()->createTable($table, $schema);
      \Drupal::messenger()->addMessage(' Table ' . $table . ' a été crée ');
    }
  }

  private function debug($datas, $name)
  {
    if ($this->modeDebug)
      debugLog::SaveLogsDrupal($datas, $name);
  }

  /**
   * Ajout un utilisateur et connecte ce dernier.
   *
   * @param string $user_name
   * @param string $email
   */
  private function addUser(string $user_name, string $email)
  {
    $generate = new Random();
    $user = User::create();
    $user->setPassword($generate->name(12));
    $user->enforceIsNew();
    $user->setEmail($email);
    $user->setUsername($user_name);
    $user->activate();
    $user->save();
    user_login_finalize($user);
  }
}