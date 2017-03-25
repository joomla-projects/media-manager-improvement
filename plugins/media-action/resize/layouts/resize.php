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
document.addEventListener('DOMContentLoaded',  function(e){ 
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

	document.getElementById('jform_resize_width').value = Math.round(event.rect.width);
	document.getElementById('jform_resize_height').value = Math.round(event.rect.height);
});
  
var img = document.getElementById('media-edit-file');

document.getElementById('jform_resize_width').value = img.offsetWidth;
document.getElementById('jform_resize_height').value = img.offsetHeight;
}, false);
  ");