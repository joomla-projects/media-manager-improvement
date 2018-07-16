<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Session\Session;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$doc    = Factory::getDocument();
$params = ComponentHelper::getParams('com_media');

// Make sure core.js is loaded before media scripts
HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.keepalive');

// Add javascripts
//JHtml::_('script', 'media/com_media/js/mediamanager.js');

Factory::getDocument()->addScriptDeclaration('
document.addEventListener(\'WebComponentsReady\', function() {
	var elem = document.createElement(\'script\');
	elem.src = "' . \Joomla\CMS\Uri\Uri::root(false) . 'media/com_media/js/mediamanager.js";
	document.head.appendChild(elem);
});
');

// Add stylesheets
HTMLHelper::_('stylesheet', 'media/com_media/css/mediamanager.css');

// Populate the language
$this->loadTemplate('texts');

$tmpl = Factory::getApplication()->input->getCmd('tmpl');

// Load the toolbar when we are in an iframe
if ($tmpl == 'component')
{
	echo Toolbar::getInstance('toolbar')->render();
}

// Populate the media config
$config = array(
	'apiBaseUrl'              => Uri::root() . 'administrator/index.php?option=com_media&format=json',
	'csrfToken'               => Session::getFormToken(),
	'filePath'                => $params->get('file_path', 'images'),
	'fileBaseUrl'             => Uri::root() . $params->get('file_path', 'images'),
	'fileBaseRelativeUrl'     => $params->get('file_path', 'images'),
	'editViewUrl'             => Uri::root() . 'administrator/index.php?option=com_media&view=file' . (!empty($tmpl) ? ('&tmpl=' . $tmpl) : ''),
	'allowedUploadExtensions' => $params->get('upload_extensions', ''),
	'maxUploadSizeMb'         => $params->get('upload_maxsize', 10),
	'providers'               => (array) $this->providers,
	'currentPath'             => $this->currentPath,
	'isModal'                 => Factory::getApplication()->input->getCmd('tmpl', '') === 'component' ? true : false,
);
$doc->addScriptOptions('com_media', $config);
?>
<div id="com-media"></div>
