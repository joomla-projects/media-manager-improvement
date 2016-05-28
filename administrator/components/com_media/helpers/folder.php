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
 * Media Manager Folder helper
 *
 * @since  3.6
 */
class MediaHelperFolder
{
	/**
	 * @param $path
	 *
	 * @return mixed
	 */
	public static function sanitizePath($path)
	{
		// Forbid usage of ".." anyway
		$path = str_replace('..', '', $path);
		
		// Don't do this for relative paths
		if (!is_dir($path)) {
			return $path;
		}
		
		$path = realpath($path);

		if (stristr($path, COM_MEDIA_BASE) == false)
		{
			return COM_MEDIA_BASE;
		}
		
		return $path;
	}

	/**
	 * @param $path
	 *
	 * @return mixed
	 */
	public static function fromAbsoluteToRelative($path)
	{
		$path = self::sanitizePath($path);
		$path = str_replace(COM_MEDIA_BASE, '', $path);
		$path = preg_replace('/^\//', '', $path);
		
		return $path;
	}
}