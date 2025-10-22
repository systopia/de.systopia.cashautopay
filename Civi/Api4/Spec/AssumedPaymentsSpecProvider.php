<?php

declare(strict_types = 1);

namespace Civi\Api4\Spec;

use Civi\Api4\Service\Spec\Provider\Generic\SpecProviderInterface;
use Civi\Api4\Service\Spec\SpecGatherer;

/**
 * Registers the APIv4 entity "AssumedPayments" and its actions.
 */
final class AssumedPaymentsSpecProvider implements SpecProviderInterface {

  public function modifySpec(SpecGatherer $spec): void {
    $entity = $spec->entity('AssumedPayments');
    $entity->setTitle('Assumed Payments');

    // preview
    $preview = $entity->action('preview');
    $preview->setTitle('Preview assumed payments (no DB writes)');
    $preview->addParam('params', 'Array', [
      'title' => 'Parameters',
      'description' => 'run_limit, grace_days, max_catchup_cycles, debug, etc.',
      'required' => FALSE,
      'default' => [],
    ]);

    // run
    $run = $entity->action('run');
    $run->setTitle('Execute assumed payments');
    $run->addParam('params', 'Array', [
      'title' => 'Parameters',
      'description' => 'run_limit, grace_days, max_catchup_cycles, debug, etc.',
      'required' => FALSE,
      'default' => [],
    ]);
  }

}
