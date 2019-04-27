<?php

namespace Drupal\profile_split_enable\Plugin\ConfigFilter;

use Drupal\config_filter\Plugin\ConfigFilterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ProfileExtensionList;

/**
 * Provides a SplitFilter.
 *
 * @ConfigFilter(
 *   id = "profile_split_enable",
 *   label = @Translation("Profile Split Enable"),
 *   storages = {"config.storage.sync"},
 * )
 */
class ProfileOverriderFilter extends ConfigFilterBase implements ContainerFactoryPluginInterface {

  /**
   *
   * @var ProfileExtensionList Service that allows us to obtain the profile name and parent
   */
  private $profileExtensionList;

  /**
   * ProfileOverriderFilter constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param $profileExtensionList
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $profileExtensionList) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->profileExtensionList = $profileExtensionList;
  }

  /**
   * Function for dependency injection
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return \Drupal\profile_split_enable\Plugin\ConfigFilter\ProfileOverriderFilter
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      [],
      $plugin_id,
      $plugin_definition,
      $container->get('extension.list.profile')
    );
  }

  /**
   * Alter the sync storage profile when importing
   * {@inheritdoc}
   */
  public function filterReadMultiple(array $names, array $data) {
    //The DB will contain our installed profile name. Override the value read
    // from the sync directory with the currently installed profile so they match
    if (in_array('core.extension', $names)) {
      $data['core.extension']['module'][\Drupal::installProfile()] = 1000;
      $data['core.extension']['profile'] = \Drupal::installProfile();
    }
    return $data;
  }

  /**
   * Alter the active storage profile when exporting
   * {@inheritdoc}
   */
  public function filterWrite($name, array $data) {

    //overwrite the core.extension details to match the base profile since this value
    //will be in the sync directory and we do not want to alter it
    if ($name == 'core.extension') {
      $profileInfo = $this->profileExtensionList->getExtensionInfo(\Drupal::installProfile());

      if (isset($profileInfo['base profile']) && $profileInfo['base profile'] != 'lightning') {
        $data['profile'] = $profileInfo['base profile'];
      }
    }

    return $data;
  }
}

