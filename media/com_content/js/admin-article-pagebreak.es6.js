/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

(() => {
  'use strict';

  window.insertPagebreak = (editor) => {
    // Get the pagebreak title
    let title = document.getElementById('title').value;

    if (!window.parent.Joomla.getOptions('xtd-pagebreak')) {
      // Something went wrong!
      window.parent.jModalClose();
      return false;
    }

    // Get the pagebreak toc alias -- not inserting for now don't know which attribute to use..
    let alt = document.getElementById('alt').value;
    title = (title !== '') ? `title="${title}"` : '';
    alt = (alt !== '') ? `alt="${alt}"` : '';
    const tag = `<hr class="system-pagebreak" ${title} ${alt}>`;
    window.parent.Joomla.editors.instances[editor].replaceSelection(tag);
    window.parent.jModalClose();
    return false;
  };
})();
