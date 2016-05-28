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
 * Media Component File Type Video Model
 *
 * @since  3.6
 */
class MediaModelFileTypeVideo extends MediaModelFileTypeDefault implements MediaModelFileTypeInterface
{
	/**
	 * Name of this file type
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $name = 'video';

	/**
	 * File extensions supported by this file type
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $extensions = array(
		'mp4',
		'webp',
		'ogg',
	);

	/**
	 * Return the file properties of a specific file
	 *
	 * @param   string $filePath
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getProperties($filePath)
	{
		$properties            = array();
		$properties['icon_32'] = 'media/mime-icon-32/mp4.png';
		$properties['icon_16'] = 'media/mime-icon-16/mp4.png';

		return $properties;
	}
}
