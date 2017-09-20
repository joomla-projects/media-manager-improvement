<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Adapter;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Media\Administrator\Event\MediaAdapterEvent;

defined('_JEXEC') or die;

/**
 * Media Adapter Manager
 *
 * @since  __DEPLOY_VERSION__
 */
class AdapterManager
{
	/**
	 * The array of results
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $adapters = array();

	/**
	 * Setup the adapters for Media Manager
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setupAdapters()
	{
		// Get the providers
		$providers = PluginHelper::getPlugin('filesystem');

		// Fire the event to get the results
		PluginHelper::importPlugin('filesystem');
		$eventParameters = ['context' => 'AdapterManager'];
		$event = new MediaAdapterEvent('onSetupAdapterManager', $eventParameters);
		$results = (array) Factory::getApplication()->triggerEvent('onSetupAdapterManager', $event);

		$adapters = array();

		for ($i = 0, $len = count($results); $i < $len; $i++)
		{
			$adapters[$providers[$i]->name] = $results[$i];
		}

		$this->adapters = $adapters;
	}

	/**
	 * Returns an associative array of adapters with provider name as the key
	 *
	 * @return  array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getAdapters()
	{
		return $this->adapters;
	}
}
