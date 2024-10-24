<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'Machine name' PropType.
 */
#[PropType(
  id: 'machine_name',
  label: new TranslatableMarkup('Machine name'),
  description: new TranslatableMarkup('A string with restricted characters.'),
  default_source: 'textfield',
  schema: ['type' => 'string', 'pattern' => '^[A-Za-z]+\w*$'],
  priority: 100
)]
class MachineNamePropType extends PropTypePluginBase {

}
