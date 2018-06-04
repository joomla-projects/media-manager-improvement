<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Image
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Image\Filter;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Image\ImageFilter;

\JLog::add('JImageFilterBrightness is deprecated, use Joomla\Image\Filter\Brightness instead.', \JLog::WARNING, 'deprecated');

/**
 * Image Filter class adjust the brightness of an image.
 *
 * @since       11.3
 * @deprecated  5.0  Use Joomla\Image\Filter\Brightness instead
 */
class Brightness extends ImageFilter
{
	/**
	 * Method to apply a filter to an image resource.
	 *
	 * @param   array  $options  An array of options for the filter.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @throws  \InvalidArgumentException
	 * @deprecated  5.0  Use Joomla\Image\Filter\Brightness::execute() instead
	 */
	public function execute(array $options = array())
	{
		// Validate that the brightness value exists and is an integer.
		if (!isset($options[IMG_FILTER_BRIGHTNESS]) || !is_int($options[IMG_FILTER_BRIGHTNESS]))
		{
			throw new \InvalidArgumentException('No valid brightness value was given.  Expected integer.');
		}

		// Perform the brightness filter.
		imagefilter($this->handle, IMG_FILTER_BRIGHTNESS, $options[IMG_FILTER_BRIGHTNESS]);
	}
}
