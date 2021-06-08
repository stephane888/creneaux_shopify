<?php
namespace Drupal\creneaux_shopify\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\creneaux_shopify\Services\ValidPermission;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Serialization\Json;
use Drupal\creneaux_shopify\Services\ApiRest\Request as ShopifyRequest;
use Drupal\creneaux_shopify\Services\CreneauxTest;

/**
 * Returns responses for Creneaux Shopify routes.
 */
class ShopifyController extends ControllerBase {

  protected $ValidPermission;

  protected $ShopifyRequest;

  protected $CreneauxTest;

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static($container->get('creneaux_shopify.valid_permission'), $container->get('creneaux_shopify.request'), $container->get('creneaux_shopify.tests'));
  }

  function __construct(ValidPermission $ValidPermission, ShopifyRequest $ShopifyRequest, CreneauxTest $CreneauxTest)
  {
    $this->ValidPermission = $ValidPermission;
    $this->ShopifyRequest = $ShopifyRequest;
    $this->CreneauxTest = $CreneauxTest;
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
      '#markup' => $this->t('Error')
    ];
    return $build;
  }

  public function Configs()
  {
    $Store = $this->CreneauxTest->loadCurrentConfig();
    $configsCreneau = [];
    if ($Store['creneaux_configs']) {
      $configsCreneau = $Store['creneaux_configs'];
    }
    $build['content'] = [
      '#theme' => 'creneaux_shopify_configs',
      '#configsCreneau' => $configsCreneau,
      '#attached' => [
        'library' => [
          'creneaux_shopify/creneaux_shopify_configs'
        ]
      ]
    ];
    $build['content']['#attached']['drupalSettings']['creneaux_shopify'] = [
      'creneau_configs' => [],
      'creneau_types' => [],
      "creneau_filters" => []
    ];
    return $build;
  }

  public function Request($action)
  {
    $configs = $this->ShopifyRequest->request($action);
    return $this->reponse($configs, $this->ShopifyRequest->AjaxStatus->getCode(), $this->ShopifyRequest->AjaxStatus->getMessage());
  }

  public function Checks()
  {
    return $this->reponse([]);
  }

  public function Add()
  {
    return $this->reponse([]);
  }

  protected function reponse($configs, $code = null, $message = null)
  {
    if (! is_string($configs))
      $configs = Json::encode($configs);
    $reponse = new JsonResponse();
    if ($code)
      $reponse->setStatusCode($code, $message);
    $reponse->setContent($configs);
    return $reponse;
  }
}
