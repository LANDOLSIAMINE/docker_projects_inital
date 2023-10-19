<?php

/**
 * @file
 * Hooks for the pate module.
 */

/**
 * @addtogroup hooks
 * @{
 */

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Allows modules to alter the list of template elements to remove in preview.
 *
 * Modules implementing this hook will receive a $to_remove indexed list of
 * element selectors, which will ultimately be passed to the javascript code
 * that removes them from the DOM inside the iframe. Add extra selectors to
 * the list or modify the existing list, depending on your custom needs.
 *
 * Note that if this list of elements to remove isn't enough for you particular
 * use-case, you can always replicate in a custom module the page_attachments
 * implemented in pate_page_attachments(), and add your own javascript that
 * manipulates the DOM if the preview query string is present (which should
 * only be the case when loading the previewed template inside the iframe).
 *
 * @param string[] $to_remove
 *   An indexed list of selectors corresponding to elements that should be
 *   removed from the DOM inside the iframe.
 */
function hook_pate_template_elements_remove_alter(array &$to_remove) {
  $to_remove[] = '#my-new-custom-selector-string .foo .bar';
}

/**
 * Allows modules to instruct pate to skip a field that is about to be unset.
 *
 * Modules implementing this hook should return TRUE if the field should NOT
 * have its values unset when cloning from template in the mode "structure
 * only" (ie "create from empty template"). This will allow this particular
 * field to retain its value in the cloned node. All other return values will
 * be disregarded, and the field in the new node will be empty.
 *
 * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
 *   The definition of the field about to be unset.
 * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
 *   The entity this field belongs to.
 */
function hook_pate_skip_field_cleanup(FieldDefinitionInterface $field_definition, FieldableEntityInterface $entity) {
  if ($field_definition->getName() === 'field_foo_bar') {
    return TRUE;
  }
  return FALSE;
}

/**
 * @} End of "addtogroup hooks".
 */
