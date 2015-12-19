<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Media Component File Adapter FlySystem Interface
 *
 * This interface aims to be more or less identical to the FlySystem interface AdapterInterface.
 * It misses (on purpose) support for FlySystem Config, streams and visibility
 * This interface will be dropped as soon Joomla will be ready to include FlySystem.
 */
interface MediaModelFileAdapterInterfaceFlysystem
{
	/**
	 * Write a new file.
	 *
	 * @param string $path
	 * @param string $contents
	 *
	 * @return array|false false on failure file meta data on success
	 */
	public function write($path, $contents);

	/**
	 * Update a file.
	 *
	 * @param string $path
	 * @param string $contents
	 *
	 * @return array|false false on failure file meta data on success
	 */
	public function update($path, $contents);

	/**
	 * Rename a file.
	 *
	 * @param string $path
	 * @param string $newpath
	 *
	 * @return bool
	 */
	public function rename($path, $newpath);

	/**
	 * Copy a file.
	 *
	 * @param string $path
	 * @param string $newpath
	 *
	 * @return bool
	 */
	public function copy($path, $newpath);

	/**
	 * Delete a file.
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	public function delete($path);

	/**
	 * Delete a directory.
	 *
	 * @param string $dirname
	 *
	 * @return bool
	 */
	public function deleteDir($dirname);

	/**
	 * Create a directory.
	 *
	 * @param string $dirname directory name
	 *
	 * @return array|false
	 */
	public function createDir($dirname);
}
