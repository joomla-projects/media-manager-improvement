<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_wrapper
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\Wrapper\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Helper for mod_wrapper
 *
 * @since  1.5
 */
class WrapperHelper
{
	/**
	 * Gets the parameters for the wrapper
	 *
	 * @param   mixed  &$params  The parameters set in the administrator section
	 *
	 * @return  mixed  &params  The modified parameters
	 *
	 * @since   1.5
	 */
	public static function getParams(&$params)
	{
		$params->def('url', '');
		$params->def('scrolling', 'auto');
		$params->def('height', '200');
		$params->def('height_auto', '0');
		$params->def('width', '100%');
		$params->def('add', '1');
		$params->def('name', 'wrapper');

		$url = $params->get('url');

		if ($params->get('add'))
		{
			// Adds 'http://' if none is set
			if (strpos($url, '/') === 0)
			{
				// Relative URL in component. use server http_host.
				$url = 'http://' . Factory::getApplication()->input->server->get('HTTP_HOST') . $url;
			}
			elseif (strpos($url, 'http') === false && strpos($url, 'https') === false)
			{
				$url = 'http://' . $url;
			}
		}

		$load = '';

		// Auto height control
		if ($params->def('height_auto'))
		{
			$load = 'onload="iFrameHeight(this)"';
		}

		$params->set('load', $load);
		$params->set('url', $url);

		return $params;
	}
}
