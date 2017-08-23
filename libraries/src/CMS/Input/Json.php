<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Input;

use Joomla\CMS\Filter\InputFilter;

defined('JPATH_PLATFORM') or die;

/**
 * Joomla! Input JSON Class
 *
 * This class decodes a JSON string from the raw request data and makes it available via
 * the standard JInput interface.
 *
 * @since       12.2
 * @deprecated  5.0  Use Joomla\Input\Json instead
 */
class Json extends Input
{
	/**
	 * @var    string  The raw JSON string from the request.
	 * @since  12.2
	 * @deprecated  5.0  Use Joomla\Input\Json instead
	 */
	private $_raw;

	/**
	 * Constructor.
	 *
	 * @param   array  $source   Source data (Optional, default is the raw HTTP input decoded from JSON)
	 * @param   array  $options  Array of configuration parameters (Optional)
	 *
	 * @since   12.2
	 * @deprecated  5.0  Use Joomla\Input\Json instead
	 */
	public function __construct(array $source = null, array $options = array())
	{
		if (isset($options['filter']))
		{
			$this->filter = $options['filter'];
		}
		else
		{
			$this->filter = InputFilter::getInstance();
		}

		if (is_null($source))
		{
			$this->_raw = file_get_contents('php://input');
			$this->data = json_decode($this->_raw, true);
		}
		else
		{
			$this->data = & $source;
		}

		// Set the options for the class.
		$this->options = $options;
	}

	/**
	 * Gets the raw JSON string from the request.
	 *
	 * @return  string  The raw JSON string from the request.
	 *
	 * @since   12.2
	 * @deprecated  5.0  Use Joomla\Input\Json instead
	 */
	public function getRaw()
	{
		return $this->_raw;
	}
}
