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
 * Media Component File Adapter Interface
 *
 * The purpose of this interface is to guarantee compatibility of different adapters with the new file handling.
 *
 * @since  3.6
 */
interface MediaModelFileAdapterInterfaceAdapter
{
	/**
	 * Return a unique hash identifying this file
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getHash();

	/**
	 * Get the current file path
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getFilePath();

	/**
	 * Set the current file path
	 *
	 * @param string $filePath
	 *
	 * @return  $this
	 *
	 * @since   3.6
	 */
	public function setFilePath($filePath);

	/**
	 * Detect the MIME type of a specific file
	 *
	 * @param string $filePath
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getMimeType($filePath = null);
}
