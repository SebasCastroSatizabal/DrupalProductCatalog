<?php

/**
 * Contains the Drupal\product_catalog\Form\ImageSearchSettingsForm class
 */

namespace Drupal\product_catalog\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ImageSearchSettingsForm. The config form for the ImageSearch class module.
 */
class ImageSearchSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'product_catalog.image_search.settings';

  /**
   * {@inheritDoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'product_catalog_image_search_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['rapid_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('X-RapidAPI-Key'),
      '#description' => t('The X-RapidAPI-Key header for the request to JoJ Image Search'),
      '#default_value' => $config->get('rapid_key'),
    ];

    $form['rapid_host'] = [
      '#type' => 'textfield',
      '#title' => $this->t('X-RapidAPI-Host'),
      '#description' => t('The X-RapidAPI-Host header for the request to JoJ Image Search'),
      '#default_value' => $config->get('rapid_host'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config(static::SETTINGS)
      ->set('rapid_key', $form_state->getValue('rapid_key'))
      ->set('rapid_host', $form_state->getValue('rapid_host'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
