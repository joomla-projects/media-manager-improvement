<?php
/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Initialize Joomla framework
const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_BASE . '/includes/framework.php';

/**
 * Cron job to trash expired cache data.
 *
 * @since  2.5
 */
class GarbageCron extends \Joomla\CMS\Application\CliApplication
{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function doExecute()
	{
		JFactory::getCache()->gc();
	}
}

// Set up the container
JFactory::getContainer()->share(
	'GarbageCron',
	function (\Joomla\DI\Container $container)
	{
		return new GarbageCron(
			null,
			null,
			null,
			null,
			$container->get(\Joomla\Event\DispatcherInterface::class),
			$container
		);
	},
	true
);
$app = JFactory::getContainer()->get('GarbageCron');
JFactory::$application = $app;
$app->execute();
