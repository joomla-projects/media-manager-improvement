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
 * Interface defining a factory which can create Menu objects
 *
 * @since  4.0.0
 */
interface MenuFactoryInterface
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
	 */
	public function createMenu(string $client, array $options = []): AbstractMenu;
}
