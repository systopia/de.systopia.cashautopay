<?php
declare(strict_types = 1);

namespace Civi\Api4;

use Civi\Api4\Generic\AbstractEntity;
use Civi\Api4\Generic\BasicGetFieldsAction;

final class AssumedPayments extends AbstractEntity {

  public static function getInfo(): array {
    return [
      'name' => 'AssumedPayments',
      'title' => 'Assumed Payments',
      'title_plural' => 'Assumed Payments',
      'class' => self::class,
      'searchable' => 'never',
      'icon' => 'fa-money-bill',
      'primary_key' => [],
      'label_field' => '',
      'type' => 'Service',
      'paths' => [],
    ];
  }

  public static function getFields(bool $checkPermissions = TRUE): BasicGetFieldsAction {
    $action = new BasicGetFieldsAction(self::class, 'getFields');

    $fields = [
      'run_limit' => [
        'title' => 'Run limit',
        'type' => 'Integer',
        'description' => 'Maximum number of items to process',
        'required' => FALSE,
      ],
      'grace_days' => [
        'title' => 'Grace days',
        'type' => 'Integer',
        'description' => 'Grace period in days',
        'required' => FALSE,
      ],
      'from_date' => [
        'title' => 'From date',
        'type' => 'String',
        'description' => 'Start date (YYYY-MM-DD)',
        'required' => FALSE,
      ],
    ];

    if (method_exists($action, 'addField')) {
      // Builder moderno
      foreach ($fields as $name => $def) {
        $type = $def['type'] ?? 'String';
        $opts = $def;
        unset($opts['type']);
        $action->addField($name, $type, $opts);
      }
    }
    elseif (method_exists($action, 'setFields')) {
      $action->setFields($fields);
    }
    else {
      if (property_exists($action, 'fields')) {
        $action->fields = $fields;
      }
    }

    return $action;
  }

  public static function permissions(): array {
    return [
      'getActions' => 'access CiviCRM',
      'getFields'  => 'access CiviCRM',
      'preview'    => 'access CiviCRM',
      'run'        => 'administer CiviCRM',
    ];
  }

}
