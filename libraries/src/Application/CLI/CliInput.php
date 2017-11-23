<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Application\CLI;

defined('JPATH_PLATFORM') or die;

/**
 * Class CliInput
 *
 * @since       4.0.0
 * @deprecated  5.0  Use the `joomla/console` package instead
 */
class CliInput
{
	/**
	 * Get a value from standard input.
	 *
	 * @return  string  The input string from standard input.
	 *
	 * @codeCoverageIgnore
	 * @since   4.0.0
	 */
	public function in()
	{
		return rtrim(fread(STDIN, 8192), "\n\r");
	}
}
