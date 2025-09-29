<?php
declare(strict_types=1);

/*
 * This file is part of the Assumed Payments extension for CiviCRM.
 * Copyright (C) 2025 Systopia and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Civi\Api4\Spec;

use Civi\Api4\SpecProviderInterface;
use Civi\Api4\Service\Spec\SpecGatherer;
use Civi\Api4\Service\Spec\Provider\SpecProviderTrait;

/**
 * Registers the APIv4 entity "AssumedPayments" and its actions.
 */
final class AssumedPaymentsSpecProvider implements SpecProviderInterface {
  use SpecProviderTrait;

  public function modifySpec(SpecGatherer $spec): void {
    $entity = $spec->entity('AssumedPayments');
    $entity->setTitle('Assumed Payments');

    // preview
    $preview = $entity->action('preview');
    $preview->setTitle('Preview assumed payments (no DB writes)');
    $preview->addParam('params', 'Array', [
      'title' => 'Parameters',
      'description' => 'run_limit, grace_days, max_catchup_cycles, debug, etc.',
      'required' => false,
      'default' => [],
    ]);

    // run
    $run = $entity->action('run');
    $run->setTitle('Execute assumed payments');
    $run->addParam('params', 'Array', [
      'title' => 'Parameters',
      'description' => 'run_limit, grace_days, max_catchup_cycles, debug, etc.',
      'required' => false,
      'default' => [],
    ]);
  }
}
