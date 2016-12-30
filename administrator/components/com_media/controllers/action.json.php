<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Action Media Controller
 *
 * This is NO public api controller, it is internal for the com_media component only!
 *
 * @todo JUST FOR Prototyping!! Move into api.json or whatever
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaControllerAction extends JControllerLegacy
{
	public function process()
	{
		$this->sendResponse('bla');
	}

	public function preview()
	{
		$input  = JFactory::getApplication()->input;

		$plugin  = $input->getString('plugin');
		$file    = JPATH_ROOT . '/' . $input->getString('file');
		$options = $input->post->getArray();

		$plugin = JPluginHelper::getPlugin('media-action', $plugin);

		// Load Plugin @todo improve
		include_once JPATH_ROOT . '/plugins/media-action/' . $plugin->name . '/' . $plugin->name . '.php';
		$className = 'PlgMediaAction' . ucfirst($plugin->name);

		/** @var MediaAction $pluginObj */
		$pluginObj = new $className($plugin->name);

		$image = imagecreatefrompng($file);

		$image = $pluginObj->process($image, $options);

		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);

		exit(1);
	}

	/**
	 * Send the given data as JSON response in the following format:
	 *
	 * {"success":true,"message":"ok","messages":null,"data":[{"type":"dir","name":"banners","path":"//"}]}
	 *
	 * @param   mixed  $data  The data to send
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function sendResponse($data = null)
	{
		echo new JResponseJson($data);
	}
}
