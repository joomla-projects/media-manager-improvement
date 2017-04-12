<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  FileSystem.Local
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once 'adapter/adapter.php';

/**
 * FileSystem Local plugin.
 * This plugin will be used to manipulate local file system.
 *
 * @package  FileSystem.Local
 * @since    __DEPLOY_VERSION__
 */
class PlgFileSystemLocal extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;


	/**
	 * Returns a local media adapter
	 *
	 * @param $path - The path used to be initialize a MediaFileAdapter
	 * @return MediaFileAdapterLocal
	 * @since version __DEPLOY_VERSION__
	 */
	public function onFileSystemGetAdapters($path)
	{
		return new MediaFileAdapterLocal($path);
	}
}