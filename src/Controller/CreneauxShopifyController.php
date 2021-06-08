<?php
namespace Drupal\creneaux_shopify\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Serialization\Json;
use Drupal\creneaux_shopify\Services\CreneauxTest;

/**
 * Returns responses for Creneaux Shopify routes.
 */
class CreneauxShopifyController extends ControllerBase {

  protected $CreneauxTest;

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static($container->get('creneaux_shopify.tests'));
  }

  function __construct(CreneauxTest $CreneauxTest)
  {
    $this->CreneauxTest = $CreneauxTest;
  }

  /**
   * Builds the response.
   * enregistre la config tests.
   */
  public function TestConfig()
  {
    return $this->reponse($this->CreneauxTest->saveCreneauxTests(), $this->CreneauxTest->AjaxStatus->getCode(), $this->CreneauxTest->AjaxStatus->getMessage());
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
