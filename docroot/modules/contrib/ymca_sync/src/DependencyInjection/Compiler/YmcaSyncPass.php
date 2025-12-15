<?php

namespace Drupal\ymca_sync\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class YmcaSyncPass.
 *
 * @package Drupal\ymca_sync\DependencyInjection\Compiler
 */
class YmcaSyncPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container) {
    $syncers = [];
    foreach (array_keys($container->findTaggedServiceIds('syncer')) as $id) {
      $syncers[] = $id;
    }

    $definition = new Definition('Drupal\ymca_sync\SyncRepository');
    // As of Symfony 5.2 all services are private by default, but in Drupal
    // services are still public. See https://www.drupal.org/node/3194517
    $definition->setPublic(TRUE);
    $container->setDefinition('ymca_sync.sync_repository', $definition);

    $definition = $container->getDefinition('ymca_sync.sync_repository');
    $definition->addArgument($syncers);
  }

}
