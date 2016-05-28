<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * File table
 *
 * @since  3.6
 */
class MediaTableFile extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver &$db Database connector object
	 *
	 * @since   3.6
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__media_files', 'id', $db);
	}
}
