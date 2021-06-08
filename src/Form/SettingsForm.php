<?php
namespace Drupal\creneaux_shopify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\creneaux_shopify\Services\ShopifyHost;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Configure Creneaux Shopify settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Drupal\creneaux_shopify\Services\ShopifyHost definition.
   *
   * @var ShopifyHost $currentUser
   */
  protected $ShopifyHost;

  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->ShopifyHost = $container->get('creneaux_shopify.shopify_host');
    return $instance;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'creneaux_shopify_settings';
  }

  /**
   *
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'creneaux_shopify.settings'
    ];
  }

  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    // creation des base de données.
    $this->ShopifyHost->table();
    //
    $form['key_app'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Clé API'),
      '#default_value' => $this->config('creneaux_shopify.settings')->get('key_app'),
      '#required' => true
    ];

    $form['secret_app'] = [
      '#type' => 'textfield',
      '#title' => $this->t("Clé secrète de l'API"),
      '#default_value' => $this->config('creneaux_shopify.settings')->get('secret_app'),
      '#required' => true
    ];

    $form['redirect_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t(" URL de redirection autorisée(s) "),
      '#default_value' => $this->config('creneaux_shopify.settings')->get('redirect_uri'),
      '#required' => true
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   *
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if ($form_state->getValue('key_app') != 'key_app') {
      // $form_state->setErrorByName('key_app', $this->t('The value is not correct.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $this->config('creneaux_shopify.settings')
      ->set('key_app', $form_state->getValue('key_app'))
      ->set('secret_app', $form_state->getValue('secret_app'))
      ->set('redirect_uri', $form_state->getValue('redirect_uri'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
