<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Media Manager Editor Controller
 */
class MediaControllerEditor extends JControllerLegacy
{
	/**
	 * Proof of pudding for Media Editor plugins
	 * 1) Install the GitHub plugin yireo/plg_media-editor_plugin and enable it
	 * 2) Access the backend URL index.php?option=com_media&task=editor.display&plugin=example&file=images/powered_by.png
	 *
	 * @throws Exception
	 */
	public function display()
	{
		$app = JFactory::getApplication();

		$html       = null;
		$filePath   = $app->input->getPath('file');
		$pluginName = $app->input->getCmd('plugin');

		if (empty($pluginName))
		{
			throw new RuntimeException('No plugin identified');
		}

		$plugin = $this->loadPlugin($pluginName);

		if ($plugin == false)
		{
			throw new RuntimeException('Unable to load plugin from plugin data');
		}

		if (method_exists($plugin, 'onMediaEditorDisplay') == false)
		{
			throw new RuntimeException('Unsupported plugin');
		}

		$postUrl = 'index.php?option=com_media&task=editor.post&plugin=' . $pluginName;
		$html    = $plugin->onMediaEditorDisplay($filePath, $postUrl);

		// @todo: Create actually a view to generate a HTML container for this $html
		echo $html;

		$app->close();
	}

	/**
	 *  Proof of pudding for Media Editor plugins
	 */
	public function post()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();

		$file       = $app->input->getString('file');
		$pluginName = $app->input->getCmd('plugin');
		$plugin     = $this->loadPlugin($pluginName);

		if ($plugin == false)
		{
			throw new RuntimException('Unsupported plugin');
		}

		$filePath = JPATH_ROOT . '/' . $file;
		$plugin->onMediaEditorProcess($filePath);

		$app->close();
	}

	/**
	 * Helper method to load a specific Media Editor plugin from its name
	 *
	 * @param $pluginName
	 *
	 * @return bool|JPlugin
	 */
	protected function loadPlugin($pluginName)
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