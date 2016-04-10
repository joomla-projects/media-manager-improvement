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
 * HTML View class for the Media component
 *
 * @since  1.0
 */
class MediaViewFilesCommon extends JViewLegacy
{
	/**
	 * @var array
	 */
	protected $folders = array();

	/**
	 * @var array
	 */
	protected $files = array();

	/**
	 * @var mixed
	 */
	protected $_tmp_folder;

	/**
	 * @var mixed
	 */
	protected $_tmp_file;

	/**
	 * Set the active folder
	 *
	 * @param   integer  $index  Folder position
	 *
	 * @return  void
	 *
	 * @since   1.0
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
	 * @return mixed
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
	 * @since   1.0
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
	 * @since   1.0
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
