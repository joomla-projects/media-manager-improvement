<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Application\CLI\Output;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Application\CLI\CliOutput;

/**
 * Output handler for writing command line output to the stdout interface
 *
 * @since       4.0.0
 * @deprecated  5.0  Use the `joomla/console` package instead
 */
class Xml extends CliOutput
{
	/**
	 * Write a string to standard output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  $this
	 *
	 * @since   4.0.0
	 * @throws  \RuntimeException
	 * @codeCoverageIgnore
	 */
	public function out($text = '', $nl = true)
	{
		fwrite(STDOUT, $text . ($nl ? "\n" : null));

		return $this;
	}
}
