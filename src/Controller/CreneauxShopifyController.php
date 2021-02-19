<?php

namespace Drupal\creneaux_shopify\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Creneaux Shopify routes.
 */
class CreneauxShopifyController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
