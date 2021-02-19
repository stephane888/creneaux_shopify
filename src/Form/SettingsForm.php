<?php
namespace Drupal\creneaux_shopify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Creneaux Shopify settings for this site.
 */
class SettingsForm extends ConfigFormBase {

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
    $form['key_app'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Clé API'),
      '#default_value' => $this->config('creneaux_shopify.settings')->get('key_app'),
      '#required' => true
    ];

    $form['secret_app'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Clé API'),
      '#default_value' => $this->config('creneaux_shopify.settings')->get('secret_app'),
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
      ->save();
    parent::submitForm($form, $form_state);
  }
}
