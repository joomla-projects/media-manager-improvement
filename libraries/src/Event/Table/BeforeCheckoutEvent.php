<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Event\Table;

defined('JPATH_PLATFORM') or die;

use BadMethodCallException;

/**
 * Event class for JTable's onBeforeCheckout event
 *
 * @since  4.0.0
 */
class BeforeCheckoutEvent extends AbstractEvent
{
	/**
	 * Constructor.
	 *
	 * Mandatory arguments:
	 * subject		JTableInterface	The table we are operating on
	 * userId		integer			The Id of the user checking out the row.
	 * pk			mixed			An optional primary key value to check out.
	 *
	 * @param   string  $name       The event name.
	 * @param   array   $arguments  The event arguments.
	 *
	 * @throws  BadMethodCallException
	 */
	public function __construct($name, array $arguments = array())
	{
		if (!array_key_exists('userId', $arguments))
		{
			throw new BadMethodCallException("Argument 'userId' is required for event $name");
		}

		if (!array_key_exists('pk', $arguments))
		{
			throw new BadMethodCallException("Argument 'pk' is required for event $name");
		}

		parent::__construct($name, $arguments);
	}

	/**
	 * Setter for the userId argument
	 *
	 * @param   mixed  $value  The value to set
	 *
	 * @return  mixed
	 *
	 * @throws  BadMethodCallException  if the argument is not of the expected type
	 */
	protected function setUserId($value)
	{
		if (!is_numeric($value) || empty($value))
		{
			throw new BadMethodCallException("Argument 'userId' of event {$this->name} must be an integer");
		}

		return (int) $value;
	}
}
