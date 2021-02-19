<?php

namespace Drupal\creneaux_shopify\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "creneaux_shopify_example",
 *   admin_label = @Translation("Example"),
 *   category = @Translation("Creneaux Shopify")
 * )
 */
class ExampleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
