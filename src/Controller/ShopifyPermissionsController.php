<?php
namespace Drupal\creneaux_shopify\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\creneaux_shopify\Services\ValidPermission;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Creneaux Shopify routes.
 */
class ShopifyPermissionsController extends ControllerBase {

  protected $ValidPermission;

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static($container->get('creneaux_shopify.valid_permission'));
  }

  function __construct(ValidPermission $ValidPermission)
  {
    $this->ValidPermission = $ValidPermission;
  }

  /**
   * Builds the response.
   */
  public function build()
  {
    $result = $this->ValidPermission->getPermissions();
    if ($result) {
      return $result;
    }
    $build['content'] = [
      '#type' => 'item',
      '#markup' => 'Error <a href="/app/creneaux/shopify/configs">app</a>'
    ];
    return $build;
  }
}
