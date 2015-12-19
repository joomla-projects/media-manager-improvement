<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/editor.php';

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
	public function display($cachable = false, $urlparams = array())
	{
		$app = JFactory::getApplication();

		$html = null;
		$filePath = $app->input->getPath('file');
		$pluginName = $app->input->getCmd('plugin');


		if (empty($pluginName))
		{
			throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
		}

		$plugin = MediaHelperEditor::loadPlugin($pluginName);

		if ($plugin == false)
		{
			throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
		}

		if (method_exists($plugin, 'onMediaEditorDisplay') == false)
		{
			throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
		}

		$postUrl = 'index.php?option=com_media&task=editor.post&plugin=' . $pluginName;
		$pluginHtml = $plugin->onMediaEditorDisplay($filePath);

		$layout = new JLayoutFile('editor.form');
		$layoutData = array('plugin' => $pluginHtml, 'postUrl' => $postUrl, 'filePath' => $filePath);
		echo $layout->render($layoutData);

		$app->close();
	}

	/**
	 *  Proof of pudding for Media Editor plugins
	 */
	public function post()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();

		$file = $app->input->getString('file');
		$pluginName = $app->input->getCmd('plugin');
		$plugin = MediaHelperEditor::loadPlugin($pluginName);

		if ($plugin == false)
		{
			throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
		}

		$filePath = COM_MEDIA_BASE . '/' . $file;
		$plugin->onMediaEditorProcess($filePath);

		$layout = new JLayoutFile('editor.close');
		$layoutData = array();
		echo $layout->render($layoutData);

		$app->close();
	}
}