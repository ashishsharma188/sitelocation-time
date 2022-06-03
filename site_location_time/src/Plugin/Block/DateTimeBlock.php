<?php

namespace Drupal\site_location_time\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\site_location_time\Service\CustomService;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'DateTime' Block.
 *
 * @Block(
 *   id = "site_location_time_block",
 *   admin_label = @Translation("Location Date Time Block"),
 *   category = @Translation("Location Date Time Block"),
 * )
 */
class DateTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * Constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   ConfigFactory description.
   * @param \Drupal\site_location_time\CustomService $customSerivce
   *   CustomSerivce description.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactory $configFactory, CustomService $customSerivce) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $configFactory, $customSerivce);
    $this->configFactory = $configFactory;
    $this->customSerivce = $customSerivce;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('site_location_time.custom_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get('site_location_time.settings');
    $timezone = $config->get('zone');
    $time = $this->customSerivce->getDateTime($timezone);

    //$offset = explode('|', $timezone);

    return [
      '#theme' => 'site_location_time_template',
      '#country' => $config->get('country'),
      '#city' => $config->get('city'),
      '#timezone' => $config->get('zone'),
      '#time' => $time,
      '#cache' => [
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => [
          'site_location_time/custom_library',
        ],
        'drupalSettings' => [
          'timezone_time' => [
            'timezone' => $timezone,
          ],
        ],
      ],
    ];
  }

}
