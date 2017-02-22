<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$doc    = JFactory::getDocument();
$params = JComponentHelper::getParams('com_media');

// Make sure core.js is loaded before media scripts
JHtml::_('behavior.core');

// Populate the media config
$config = array(
	'apiBaseUrl'              => JUri::root() . 'administrator/index.php?option=com_media&format=json',
	'filePath'                => $params->get('file_path', 'images'),
	'allowedUploadExtensions' => $params->get('upload_extensions', ''),
	'maxUploadSizeMb'         => $params->get('upload_maxsize', 10),
);
$doc->addScriptOptions('com_media', $config);

// Populate the language
// TODO use JText for all language strings used by the js application

// Add javascripts
JHtml::_('script', 'media/com_media/js/mediamanager.js');


// Add stylesheets
JHtml::_('stylesheet', 'media/com_media/css/mediamanager.css');

// TODO include the font in the component media (self hosted)
JHtml::_('stylesheet', 'https://fonts.googleapis.com/icon?family=Material+Icons');
?>
<div id="com-media"></div>
