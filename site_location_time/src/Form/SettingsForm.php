<?php

namespace Drupal\site_location_time\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\site_location_time\Service\CustomService;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Create a config form to collect country, city and timezone configuration.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The custom service.
   *
   * @var \Drupal\Core\Config\CustomService
   */
  public $customSerivce;


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'config_form_custom';
  }

  /**
   * Constructor.
   */
  public function __construct(ConfigFactoryInterface $configFactory, CustomService $customSerivce) {
      parent::__construct($configFactory);
      $this->customSerivce = $customSerivce;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
          // Load the service required to construct this class.
          $container->get('config.factory'),
          $container->get('site_location_time.custom_service')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
      return ['site_location_time.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('site_location_time.settings');

    if( !empty($config->get('zone'))) {
      $timezone = $config->get('zone');
    }
    else {
      $timezone = 'America/New_York';
    }

    $zone = [
      '0' => 'Select Timezone',
      'America/Chicago' => 'America/Chicago',
      'America/New_York' => 'America/New York',
      'Asia/Tokyo' => 'Asia/Tokyo',
      'Asia/Dubai' => 'Asia/Dubai',
      'Asia/Kolkata' => 'Asia/Kolkata',
      'Europe/Amsterdam' => 'Europe/Amsterdam',
      'Europe/Oslo' => 'Europe/Oslo',
      'Europe/London' => 'Europe/London',
    ];

    $form['config'] = [
      '#type' => 'details',
      '#title' => 'ADMIN CONFIGURATION',
      '#open' => TRUE,
    ];

    $form['config']['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter Country'),
      '#default_value' => $config->get('country'),
      '#required' => TRUE,
    ];

    $form['config']['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter City'),
      '#default_value' => $config->get('city'),
      '#required' => TRUE,
    ];

    $form['config']['zone'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Timezone'),
      '#options' => $zone,
      '#default_value' => $config->get('zone') ? $config->get('zone') : '0',
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::getTimeZone',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => 'Please Wait',
        ],
      ],
    ];
    $form['config']['markup'] = [
      '#type' => 'markup',
      '#markup' => '<b>Time is:</b> <div class="result_message">' . $this->customSerivce->getDateTime($timezone) . '</div>',
      '#attributes' => [
        'id' => 'custom-result',
      ],
    ];

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
       $this->config('site_location_time.settings')
      ->set('country', $form_state->getValue('country'))
      ->set('city', $form_state->getValue('city'))
      ->set('zone', $form_state->getValue('zone'))
      ->save(TRUE);

    return parent::submitForm($form, $form_state);
  }

  /**
   * Return convert date time as per timezone selected.
   */

  public function getTimeZone(array &$form, FormStateInterface $form_state) {
    $ajaxResponse = new AjaxResponse();
    $timezone = $form_state->getValue('zone');

    if ($timezone == '0') {
      $text = 'Please select Timezone';
    }
    else {

      $text = $this->customSerivce->getDateTime($timezone);
    }

    $time = $text;
    $ajaxResponse->addCommand(new InvokeCommand('.result_message', 'html', [$time]));

    return $ajaxResponse;
  }

}
