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
 * Media Component File Adapter FlySystem Interface
 *
 * This interface aims to be more or less identical to the FlySystem interface AdapterInterface.
 * It misses (on purpose) support for FlySystem Config, streams and visibility
 * This interface will be dropped as soon Joomla will be ready to include FlySystem.
 *
 * @since  3.6
 */
interface MediaModelFileAdapterInterfaceFlysystem
{
	/**
	 * Write a new file.
	 *
	 * @param string $path
	 * @param string $contents
	 *
	 * @return  array|false false on failure file meta data on success
	 *
	 * @since   3.6
	 */
	public function write($path, $contents);

	/**
	 * Update a file.
	 *
	 * @param string $path
	 * @param string $contents
	 *
	 * @return  array|false false on failure file meta data on success
	 *
	 * @since   3.6
	 */
	public function update($path, $contents);

	/**
	 * Rename a file.
	 *
	 * @param  string $path
	 * @param  string $newpath
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function rename($path, $newpath);

	/**
	 * Copy a file.
	 *
	 * @param string $path
	 * @param string $newpath
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function copy($path, $newpath);

	/**
	 * Delete a file.
	 *
	 * @param string $path
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function delete($path);

	/**
	 * Delete a directory.
	 *
	 * @param string $dirname
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function deleteDir($dirname);

	/**
	 * Create a directory.
	 *
	 * @param string $dirname directory name
	 *
	 * @return  array|false
	 *
	 * @since   3.6
	 */
	public function createDir($dirname);
}
