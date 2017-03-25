<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.resize
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// THIS IS FOR DEMO PURPOSES ONLY!!!!

JFactory::getDocument()->addScript('//cdnjs.cloudflare.com/ajax/libs/interact.js/1.2.6/interact.min.js');

JFactory::getDocument()->addScriptDeclaration("
		interact('#media-edit-file')
  .draggable(false)
  .resizable({
    preserveAspectRatio: true,
    edges: { left: true, right: true, bottom: true, top: true }
  })
  .on('resizemove', function (event) {
    var target = event.target,
        x = (parseFloat(target.getAttribute('data-x')) || 0),
        y = (parseFloat(target.getAttribute('data-y')) || 0);

    // update the element's style
    target.style.width  = event.rect.width + 'px';
    target.style.height = event.rect.height + 'px';

    // translate when resizing from top or left edges
    x += event.deltaRect.left;
    y += event.deltaRect.top;

    target.style.webkitTransform = target.style.transform =
        'translate(' + x + 'px,' + y + 'px)';

	var fieldWidth = document.getElementById('jform_resize_width');
    fieldWidth.value = Math.round(event.rect.width);
    
	var fieldHeight = document.getElementById('jform_resize_height');
    fieldHeight.value = Math.round(event.rect.height);
  });
  
document.addEventListener('DOMContentLoaded',  function(e){ 
	var img = document.getElementById('media-edit-file');
	var fieldWidth = document.getElementById('jform_resize_width');
    fieldWidth.value = img.offsetWidth;
    
	var fieldHeight = document.getElementById('jform_resize_height');
    fieldHeight.value = img.offsetHeight;
}, false);
  ");