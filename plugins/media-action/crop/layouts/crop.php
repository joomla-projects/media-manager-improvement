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

JFactory::getDocument()->addStyleSheet('//cdnjs.cloudflare.com/ajax/libs/cropperjs/0.8.1/cropper.min.css');
JFactory::getDocument()->addScript('//cdnjs.cloudflare.com/ajax/libs/cropperjs/0.8.1/cropper.js');

JFactory::getDocument()->addScriptDeclaration("
document.addEventListener('DOMContentLoaded',  function(e){ 
var img = document.getElementById('media-edit-file');
var cropper = new Cropper(img, {
  aspectRatio: img.offsetWidth / img.offsetHeight,
  background: false,
  autoCrop: false,
  crop: function(e) {    
    document.getElementById('jform_crop_x').value = e.detail.x;
    document.getElementById('jform_crop_y').value = e.detail.y;
    document.getElementById('jform_crop_width').value = e.detail.width;
    document.getElementById('jform_crop_height').value = e.detail.height;
  }
});

document.getElementById('jform_crop_x').value = 0;
document.getElementById('jform_crop_y').value = 0;
document.getElementById('jform_crop_width').value = img.offsetWidth;
document.getElementById('jform_crop_height').value = img.offsetHeight;
}, false);
  ");