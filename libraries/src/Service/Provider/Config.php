<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Service\Provider;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

/**
 * Service provider for the application's config dependency
 *
 * @since  4.0
 */
class Config implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	public function register(Container $container)
	{
		$container->alias('config', 'JConfig')
			->share(
				'JConfig',
				function (Container $container)
				{
					if (!file_exists(JPATH_CONFIGURATION . '/configuration.php'))
					{
						return new Registry;
					}

					\JLoader::register('JConfig', JPATH_CONFIGURATION . '/configuration.php');

					if (!class_exists('JConfig'))
					{
						throw new \RuntimeException('Configuration class does not exist.');
					}

					return new Registry(new \JConfig);
				},
				true
			);
	}
}
