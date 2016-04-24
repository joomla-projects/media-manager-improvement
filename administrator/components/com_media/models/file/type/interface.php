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
 * Media Component File Type Interface
 *
 * @since  3.6
 */
interface MediaModelFileTypeInterface
{
	/**
	 * Return the name of this file type
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getName();

	/**
	 * Return a listing of supported file extensions
	 *
	 * @return  mixed
	 *
	 * @since   3.6
	 */
	public function getExtensions();

	/**
	 * Return a listing of supported MIME types
	 *
	 * @return  mixed
	 *
	 * @since   3.6
	 */
	public function getMimeTypes();

	/**
	 * Return the file properties of a specific file
	 *
	 * @return array
	 *
	 * @since   3.6
	 */
	public function getProperties($filePath);
}
