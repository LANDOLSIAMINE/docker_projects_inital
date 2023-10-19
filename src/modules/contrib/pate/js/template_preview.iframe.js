/**
 * @file template_preview.iframe.js
 *
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.PateTemplatePreviewIframeBehavior = {
    attach: function (context) {
      $(once('pate-template-iframe-preview', 'body', context)).each(function () {
        if (typeof drupalSettings.pate !== 'undefined'
          && typeof drupalSettings.pate.template_elements_to_remove !== 'undefined'
          && drupalSettings.pate.template_elements_to_remove.length > 0) {
          const to_remove = drupalSettings.pate.template_elements_to_remove;
          for (const key in to_remove) {
            $(to_remove[key]).remove();
          }
        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
