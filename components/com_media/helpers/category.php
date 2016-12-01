<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Media Component Category Tree
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaCategories extends JCategories
{
	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		$options['table'] = '#__media_files';
		$options['extension'] = 'com_media';

		parent::__construct($options);
	}
}
