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
class MediaModelFolder
{
	/**
	 * Folder name
	 *
	 * @var null
	 */
	protected $name = null;

	/**
	 * Relative folder path
	 *
	 * @var null
	 */
	protected $path = null;

	/**
	 * Absolute folder path
	 *
	 * @var null
	 */
	protected $full_path = null;

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
	 * List of folders that should not be touched
	 *
	 * @var array
	 */
	protected $skipList = array(
		'.svn',
		'.git',
		'.gitignore',
		'CVS',
		'.DS_Store',
		'__MACOSX',
		'index.html',
		'desktop.ini',
	);

	/**
	 * Load a specific folder
	 *
	 * @param $path
	 *
	 * @return MediaModelFolder
	 *
	 * @throws InvalidArgumentException
	 */
	public function load($path)
	{
		if (empty($path))
		{
			throw new InvalidArgumentException(JText::_('COM_MEDIA_ERROR_WARNFILENAME'));
		}

		$full_path = realpath(COM_MEDIA_BASE . '/' . $path);

		if (!is_dir($full_path))
		{
			throw new InvalidArgumentException(JText::_('COM_MEDIA_ERROR_WARNFILENAME'));
		}

		$this->path      = $path;
		$this->full_path = $full_path;

		return $this;
	}

	/**
	 * Load a specific folder
	 *
	 * @param $path
	 *
	 * @return MediaModelFolder
	 *
	 * @throws InvalidArgumentException
	 */
	public function loadByPath($path)
	{
		return $this->load($path);
	}

	/**
	 * Create a new path
	 */
	public function create($path)
	{
		$this->path = $path;

		$this->checkNameSafe();

		$full_path = COM_MEDIA_BASE . '/' . $path;

		if (is_dir($full_path))
		{
			// @todo: Add to language pack
			throw new Exception(JText::sprintf('COM_MEDIA_ERROR_FOLDER_ALREADY_EXISTS'));
		}

		JFolder::create($full_path);
		
		return $this->load($this->path);
	}
	
	/**
	 * Method to delete an object
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function delete()
	{
		$this->checkNameSafe();

		$contents = JFolder::files($this->full_path, '.', true, false, $this->skipList);

		if (!empty($contents))
		{
			throw new Exception(JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', $this->path));
		}

		// Trigger the onContentBeforeDelete event
		$folderObject = new JObject(array('filepath' => $this->full_path));
		$result       = $this->triggerEvent('onContentBeforeDelete', array('com_media.folder', &$folderObject));

		if (in_array(false, $result, true))
		{
			// There are some errors in the plugins
			$errors = $folderObject->getErrors();
			throw new Exception(JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));
		}

		$rt = JFolder::delete($this->full_path);

		// Trigger the onContentAfterDelete event.
		$this->triggerEvent('onContentAfterDelete', array('com_media.folder', &$folderObject));

		return $rt;
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
		$this->files   = $this->getFilesModel()
			->setCurrentFolder($currentFolder)
			->getFiles();

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
		$this->folders = $this->getFoldersModel()
			->setCurrentFolder($currentFolder)
			->getFolders();

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
		$current       = (string) $this->getState('folder');
		$currentFolder = COM_MEDIA_BASE . ((strlen($current) > 0) ? '/' . $current : '');

		return $currentFolder;
	}

	/**
	 * Triggers the specified event
	 *
	 * @param string $eventName
	 * @param array  $eventArguments
	 *
	 * @return mixed
	 */
	protected function triggerEvent($eventName, $eventArguments)
	{
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();

		return $dispatcher->trigger($eventName, $eventArguments);
	}
	
	/**
	 * @throws Exception
	 */
	protected function checkNameSafe()
	{
		$basePath = basename($this->path);

		if ($basePath !== JFile::makeSafe($basePath))
		{
			// Filename is not safe
			$folderName = htmlspecialchars($this->path, ENT_COMPAT, 'UTF-8');
			// @todo: Change error text
			throw new Exception(JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', $folderName));
		}
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
		return JModelLegacy::getInstance('files', 'MediaModel');
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
		return JModelLegacy::getInstance('folders', 'MediaModel');
	}
}
