<?php

namespace Drupal\profile_split_enable\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Enable splits for this profile.
 */
class SplitEnableSubscriber implements EventSubscriberInterface {

  use ContainerAwareTrait;

  /**
   * Redirect pattern based url.
   *
   * @param Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The response event we are responding to.
   */
  public function splitEnable(GetResponseEvent $event) {

    global $config;

    $split_filename_prefix = 'config_split.config_split';

    $active_profile = $this->container->getParameter('install_profile');
    $config["$split_filename_prefix.$active_profile"]['status'] = TRUE;
  }

  /**
   * Listen to kernel.request events and call splitEnable.
   *
   * {@inheritdoc}
   *
   * @return array
   *   Event names to listen to (key) and methods to call (value)
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['splitEnable'];
    return $events;
  }

}
