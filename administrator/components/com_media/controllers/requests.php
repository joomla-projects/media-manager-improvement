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
 * The Request Controller
 * @todo  Webservice
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaControllerRequests extends JControllerLegacy
{
	/**
	 * Called by the plugin through JavaScript
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process()
	{
		echo new JResponseJson(array('success' => 'true'));

		// @todo fix @format=json not working with routing
		jexit(0);
	}
}
