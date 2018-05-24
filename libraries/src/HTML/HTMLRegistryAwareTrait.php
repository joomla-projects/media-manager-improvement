<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\HTML;

defined('_JEXEC') or die;

/**
 * Defines the trait for a HTML Registry aware class.
 *
 * @since  4.0.0
 */
trait HTMLRegistryAwareTrait
{
	/**
	 * The registry
	 *
	 * @var    Registry
	 * @since  4.0.0
	 */
	private $registry;

	/**
	 * Get the registry.
	 *
	 * @return  Registry
	 *
	 * @since   4.0.0
	 * @throws  \UnexpectedValueException May be thrown if the registry has not been set.
	 */
	public function getRegistry()
	{
		if ($this->registry)
		{
			return $this->registry;
		}

		throw new \UnexpectedValueException('HTML registry not set in ' . __CLASS__);
	}

	/**
	 * Set the registry to use.
	 *
	 * @param   Registry  $registry  The registry
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function setRegistry(Registry $registry = null)
	{
		$this->registry = $registry;
	}
}
