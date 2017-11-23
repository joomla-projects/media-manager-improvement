<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Menu;

defined('_JEXEC') or die;

/**
 * Default factory for creating Menu objects
 *
 * @since  4.0.0
 */
class MenuFactory implements MenuFactoryInterface
{
	/**
	 * Creates a new Menu object for the requested format.
	 *
	 * @param   string  $client   The name of the client
	 * @param   array   $options  An associative array of options
	 *
	 * @return  AbstractMenu
	 *
	 * @since   4.0.0
	 * @throws  \InvalidArgumentException
	 */
	public function createMenu(string $client, array $options = []): AbstractMenu
	{
		// Create a Menu object
		$classname = __NAMESPACE__ . '\\' . ucfirst(strtolower($client)) . 'Menu';

		if (!class_exists($classname))
		{
			throw new \InvalidArgumentException(\JText::sprintf('JLIB_APPLICATION_ERROR_MENU_LOAD', $client), 500);
		}

		return new $classname($options);
	}
}
