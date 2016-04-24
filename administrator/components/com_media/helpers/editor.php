<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Media Manager Editor helper
 *
 * @since  3.6
 */
class MediaHelperEditor
{
	/**
	 * Helper method to load a specific Media Editor plugin from its name
	 *
	 * @param   pluginName
	 *
	 * @return  bool|JPlugin
	 *
	 * @since   3.6
	 */
	static public function loadPlugin($pluginName)
	{
		$pluginData = JPluginHelper::getPlugin('media-editor', $pluginName);

		if (empty($pluginData))
		{
			return false;
		}

		$fileName = JPATH_ROOT . '/plugins/media-editor/' . $pluginData->name . '/' . $pluginData->name . '.php';

		include_once $fileName;

		$className = 'PlgMediaEditor' . ucfirst($pluginData->name);
		$plugin    = null;

		if (!class_exists($className))
		{
			return false;
		}

		$dispatcher = JEventDispatcher::getInstance();
		$plugin     = new $className($dispatcher, (array) $pluginData);

		if (!$plugin instanceof JPlugin)
		{
			return false;
		}

		return $plugin;
	}
}
