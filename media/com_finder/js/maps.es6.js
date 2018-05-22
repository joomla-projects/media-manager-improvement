/**
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

Joomla = window.Joomla || {};

(() => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    Joomla.submitbutton = (pressbutton) => {
      // TODO replace with joomla-alert
      if (pressbutton === 'map.delete' && !window.confirm(Joomla.JText._('COM_FINDER_MAPS_CONFIRM_DELETE_PROMPT'))) {
        return false;
      }
      Joomla.submitform(pressbutton);
      return true;
    };
  });
})();
