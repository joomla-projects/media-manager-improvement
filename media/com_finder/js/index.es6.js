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
      if (pressbutton === 'index.purge' && !window.confirm(Joomla.JText._('COM_FINDER_INDEX_CONFIRM_PURGE_PROMPT'))) {
        return false;
      }
      // TODO replace with joomla-alert
      if (pressbutton === 'index.delete' && !window.confirm(Joomla.JText._('COM_FINDER_INDEX_CONFIRM_DELETE_PROMPT'))) {
        return false;
      }
      Joomla.submitform(pressbutton);
      return true;
    };
  });
})();
