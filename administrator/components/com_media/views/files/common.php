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
 * HTML View class for the Media component
 *
 * @since  3.6
 */
class MediaViewFilesCommon extends JViewLegacy
{
	/**
	 * The folders
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $folders = array();

	/**
	 * The files
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $files = array();

	/**
	 * The temp folder
	 *
	 * @var    mixed
	 * @since  3.6
	 */
	protected $_tmp_folder;

	/**
	 * The temp file
	 *
	 * @var    mixed
	 * @since  3.6
	 */
	protected $_tmp_file;

	/**
	 * Set the active folder
	 *
	 * @param   integer  $index  Folder position
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	public function setFolder($index = 0)
	{
		if (isset($this->folders[$index]))
		{
			$this->_tmp_folder = $this->folders[$index];
		}
		else
		{
			$this->_tmp_folder = new JObject;
		}
	}

	/**
	 * Get the active folder
	 *
	 * @return  mixed  The active folder
	 *
	 * @since   3.6
	 */
	public function getFolder()
	{
		return $this->_tmp_folder;
	}

	/**
	 * Set the active folder
	 *
	 * @param   string  $folderName  Folder name
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	public function setFolderByName($folderName)
	{
		if (isset($this->folders['children'][$folderName]))
		{
			$this->_tmp_folder = $this->folders['children'][$folderName];
		}
		else
		{
			$this->_tmp_folder = new JObject;
		}
	}

	/**
	 * Set the active image
	 *
	 * @param   integer  $index  Image position
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	public function setFile($index = 0)
	{
		if (isset($this->files[$index]))
		{
			$this->_tmp_file = $this->files[$index];
		}
		else
		{
			$this->_tmp_file = new JObject;
		}
	}
}
