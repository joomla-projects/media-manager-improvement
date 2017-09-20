<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Provider;

use Joomla\Component\Media\Administrator\Adapter\AdapterInterface;

defined('_JEXEC') or die;

/**
 * Media Adapter Manager
 *
 * @since  __DEPLOY_VERSION__
 */
class ProviderManager
{
	/**
	 * The array of providers
	 *
	 * @var  ProviderInterface[]
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $providers = array();

	/**
	 * Returns an associative array of adapters with provider name as the key
	 *
	 * @return  ProviderInterface[]
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getProviders()
	{
		return $this->providers;
	}

	/**
	 * Register a provider into the ProviderManager
	 *
	 * @param   ProviderInterface  $provider  The provider to be registered
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function registerProvider(ProviderInterface $provider)
	{
		$this->providers[$provider->getID()] = $provider;
	}

	/**
	 * Returns the provider for a particular ID
	 *
	 * @param   string  $id  The ID for the provider
	 *
	 * @return  ProviderInterface
	 *
	 * @throws \Exception
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getProvider($id)
	{
		if (!isset($this->providers[$id]))
		{
			throw new \Exception("Media Provider not found");
		}

		return $this->providers[$id];
	}

	/**
	 * Returns an adapter for an account
	 *
	 * @param   string  $name  The name of an adapter
	 *
	 * @return  AdapterInterface
	 *
	 * @throws \Exception
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getAdapter($name)
	{
		list($provider, $account) = array_pad(explode('-', $name, 2), 2, null);

		if ($account == null)
		{
			throw new \Exception('Account was not set');
		}

		if (!isset($this->getProvider($provider)->getAdapters()[$account]))
		{
			throw new \Exception("The account was not found");
		}

		return $this->getProvider($provider)->getAdapters()[$account];
	}
}
