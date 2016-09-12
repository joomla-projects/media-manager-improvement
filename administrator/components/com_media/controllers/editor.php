<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_COMPONENT . '/helpers/editor.php';

/**
 * Media Manager Editor Controller
 */
class MediaControllerEditor extends MediaController
{
	/**
	 * Post action that can be picked up upon by Media Editor plugins
	 *
	 * @since  3.6
	 */
	public function post()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$file       = $this->input->getString('file');
		$pluginName = $this->input->getCmd('plugin');
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

		$app = JFactory::getApplication();
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
		$file   = $this->input->get('file', '', 'path');
		$folder = '';

		if (!empty($file))
		{
			$folder = dirname($file);
		}

		$url         = $this->getMediaUrl() . '&view=folders&folder=' . $folder;
		$redirectUrl = JRoute::_($url);
		$this->setRedirect($redirectUrl);
	}
}
