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
 * Media file adapter interface.
 *
 * @since  __DEPLOY_VERSION__
 */
interface MediaFileAdapterInterface
{
	/**
	 * Returns the folders and files for the given path. The returned objects
	 * have the following properties available:
	 * - type: The type can be file or dir
	 * - name: The name of the file
	 * - path: The relative path to the root
	 *
	 * If the type is file, then some additional properties are available:
	 * - extension: The file extension
	 * - size:      The size of the file
	 *
	 * @param   string  $path  The folder
	 *
	 * @return  stdClass[]
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  Exception
	 */
	public function getFiles($path = '/');

	/**
	 * Deletes the folder or file of the given path.
	 *
	 * @param   string  $path  The path to the file or folder
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  Exception
	 */
	public function delete($path);

	/**
	 * Returns a file in the local filesystem for the given path.
	 *
	 * @param   string  $path  The path to the file
	 *
	 * @return  string  The local file path
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  Exception
	 */
	public function getLocalFilePath($path);
}
