<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Admin\Administrator\Service\HTML;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Utility class working with system
 *
 * @since  1.6
 */
class System
{
	/**
	 * Method to generate a string message for a value
	 *
	 * @param   string  $val  a php ini value
	 *
	 * @return  string html code
	 */
	public function server($val)
	{
		return !empty($val) ? $val : Text::_('COM_ADMIN_NA');
	}
}
