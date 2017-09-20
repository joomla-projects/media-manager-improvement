<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Adapter;

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
	 * AdapterManager constructor.
	 *
	 * @param   array  $adapters  Adapters to hold
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function __construct(array $adapters)
	{
		$this->setAdapters($adapters);
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

	/**
	 * Sets adapters for AdapterManager
	 *
	 * @param   array  $adapters  An array of adapters to be hold
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setAdapters($adapters)
	{
		$this->adapters = $adapters;
	}
}
