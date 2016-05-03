<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
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
	 *
	 * @since  3.6
	 */
	public function post()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();

		$file       = $app->input->getString('file');
		$pluginName = $app->input->getCmd('plugin');
		$plugin     = MediaHelperEditor::loadPlugin($pluginName);

		if ($plugin == false)
		{
			throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
		}

		$filePath    = COM_MEDIA_BASE . '/' . $file;
		$redirectUrl = $plugin->onMediaEditorProcess($filePath);

		$layout     = new JLayoutFile('editor.close');
		$layoutData = array('redirectUrl' => $redirectUrl);

		echo $layout->render($layoutData);

		$app->close();
	}

	/**
	 * Redirect back to the Media Manager
	 *
	 * @throws  Exception
	 *
	 * @since   3.6
	 */
	public function cancel()
	{
		$redirectUrl = JRoute::_('index.php?option=com_media');

		$app = JFactory::getApplication();
		$app->redirect($redirectUrl);
		$app->close();
	}
}
