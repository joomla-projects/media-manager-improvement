<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Media Component Folder Model
 *
 * @since  3.6
 */
class MediaModelFolder extends JModelLegacy
{
	/**
	 * Identifier
	 *
	 * @var    int
	 * @since  3.6
	 */
	protected $id = null;

	/**
	 * Lists the files in a folder
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $files = array();

	/**
	 * Lists the subfolders in a folder
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $folders = array();

	/**
	 * Method to get model state variables
	 *
	 * @param   string $property Optional parameter name
	 * @param   mixed  $default  Optional default value
	 *
	 * @return  object  The property where specified, the state object where omitted
	 *
	 * @since   3.6
	 */
	public function getState($property = null, $default = null)
	{
		static $set;

		if (!$set)
		{
			$input  = JFactory::getApplication()->input;
			$folder = $input->get('folder', '', 'path');
			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	/**
	 * Build browsable list of files
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getFiles()
	{
		if (!empty($this->files))
		{
			return $this->files;
		}

		$currentFolder = $this->getCurrentFolder();
		$this->files = $this->getFilesModel()->setCurrentFolder($currentFolder)->getFiles();

		return $this->files;
	}

	/**
	 * Build browsable list of files
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getFolders()
	{
		if (!empty($this->folders))
		{
			return $this->folders;
		}

		$currentFolder = $this->getCurrentFolder();
		$this->folders = $this->getFoldersModel()->setCurrentFolder($currentFolder)->getFolders();

		return $this->folders;
	}

	/**
	 * Return the current folder
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getCurrentFolder()
	{
		$current = (string) $this->getState('folder');
		$currentFolder  = COM_MEDIA_BASE . ((strlen($current) > 0) ? '/' . $current : '');

		return $currentFolder;
	}

	/**
	 * Return the files model
	 *
	 * @return  MediaModelFiles
	 *
	 * @since   3.6
	 */
	protected function getFilesModel()
	{
		return new MediaModelFiles;
	}

	/**
	 * Return the folder model
	 *
	 * @return  MediaModelFolders
	 *
	 * @since   3.6
	 */
	protected function getFoldersModel()
	{
		return new MediaModelFolders;
	}
}
