<?php
namespace Drupal\creneaux_shopify\Services;

use Symfony\Component\HttpFoundation\RequestStack;

class GetPermission {

  protected $request;

  function __construct(RequestStack $RequestStack)
  {
    $this->request = $RequestStack->getCurrentRequest();
  }
}