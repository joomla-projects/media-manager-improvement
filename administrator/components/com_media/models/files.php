<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once __DIR__ . '/file.php';

/**
 * Media Component Files Model
 */
class MediaModelFiles extends JModelLegacy
{
	/**
	 * Lists the files in a folder
	 *
	 * @var array
	 */
	protected $files = array();

	/**
	 * Folder to search for files
	 *
	 * @var string
	 */
	protected $currentFolder = '';

	/**
	 * Get the current folder
	 *
	 * @return string
	 */
	public function getCurrentFolder()
	{
		return $this->currentFolder;
	}

	/**
	 * Set the current folder
	 *
	 * @param string $folder
	 *
	 * @return MediaModelFiles
	 */
	public function setCurrentFolder($currentFolder)
	{
		$this->currentFolder = $currentFolder;

		return $this;
	}

	/**
	 * Build browsable list of files with pagination support
	 *
	 * @return  array
	 */
	public function getFiles($offset = 0, $filesNo = 0)
	{
		$files = $this->loadFiles();

		if ($filesNo == 0)
		{
			return $files;
		}

		return array_slice($files, $offset, $filesNo);
	}

	/**
	 * Build browsable list of files
	 *
	 * @return  array
	 */
	protected function loadFiles()
	{
		if (!empty($this->files))
		{
			return $this->files;
		}

		$currentFolder = COM_MEDIA_BASE;

		if (!file_exists($currentFolder))
		{
			return $this->files;
		}

		if (!file_exists($currentFolder))
		{
			return $this->files;
		}

		$fileList = JFolder::files($currentFolder);
		$fileHashes = array();
		$storedFiles = $this->getStoredFiles($currentFolder);

		// Iterate over the files if they exist
		if ($fileList !== false)
		{
			// Add all files that are physically detected in this folder
			foreach ($fileList as $file)
			{
				// Construct the file object for use in the Media Manager
				$tmp = $this->loadObjectFromFile($file, $currentFolder, $fileHashes);

				if ($tmp == false)
				{
					continue;
				}

				$this->files[] = $tmp;
			}

			// Add all files that are in the database and are not detected in this folder
			foreach ($storedFiles as $storedFile)
			{
				// Construct the file object for use in the Media Manager
				$tmp = $this->loadObjectFromStoredFile($storedFile, $currentFolder, $fileHashes);

				if ($tmp == false)
				{
					continue;
				}

				$this->files[] = $tmp;
			}
		}

		return $this->files;
	}

	/**
	 * @param $file
	 * @param $currentFolder
	 * @param $fileHashes
	 *
	 * @return JObject
	 */
	protected function loadObjectFromFile($file, $currentFolder, $fileHashes)
	{
		$filePath = $currentFolder . '/' . $file;

		if (!$this->isFileBrowsable($filePath))
		{
			return false;
		}

		$fileModel = $this->getFileModel();
		$fileModel->setFileAdapter('local', $filePath)
			->loadByPath($filePath);

		// Construct the file object for use in the Media Manager
		$tmp = new JObject;
		$tmp->setProperties($fileModel->getFileProperties());
		$tmpHash = md5($currentFolder . '/' . $file);
		$fileHashes[] = $tmpHash;

		return $tmp;
	}

	/**
	 * @param $storedFile
	 * @param $currentFolder
	 * @param $fileHashes
	 *
	 * @return bool|JObject
	 */
	public function loadObjectFromStoredFile($storedFile, $currentFolder, $fileHashes)
	{
		$fileModel = $this->getFileModel();
		$fileModel->loadByPath($currentFolder . '/' . $storedFile->filename);

		// Skip files already detected
		$tmpHash = md5($currentFolder . '/' . $storedFile->filename);

		if (in_array($tmpHash, $fileHashes))
		{
			return false;
		}

		// Construct the file object for use in the Media Manager
		$tmp = new JObject;
		$tmp->setProperties($fileModel->getFileProperties());

		return $tmp;
	}

	/**
	 * Return a list of the files stored in the database for a specific folder
	 *
	 * @param $folder
	 *
	 * @return mixed
	 */
	public function getStoredFiles($folder)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('id', 'filename', 'path', 'md5sum', 'adapter')));
		$query->from($db->quoteName('#__media_files'));
		$query->where($db->quoteName('path') . ' = ' . $db->quote($folder));
		$query->order('ordering ASC');

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Check whether this file is browsable in the Media Manager
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	protected function isFileBrowsable($file)
	{
		$relativeFile = basename($file);

		if (!is_file($file))
		{
			return false;
		}

		if (substr($relativeFile, 0, 1) == '.')
		{
			return false;
		}

		if (strtolower($relativeFile) == 'index.html')
		{
			return false;
		}

		return true;
	}

	/**
	 * Return the file model
	 *
	 * @return MediaModelFile
	 */
	protected function getFileModel()
	{
		return new MediaModelFile;
	}
}
