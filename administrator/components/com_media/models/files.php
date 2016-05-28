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

require_once __DIR__ . '/file.php';

/**
 * Media Component Files Model
 */
class MediaModelFiles extends JModelLegacy
{
	/**
	 * Filter for filtering only image names
	 */
	const IMAGE_FILTER = '.(gif|jpg|jpeg|png)';

	/**
	 * Lists the files in a folder
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $files = array();

	/**
	 * Get the current folder
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getCurrentFolder()
	{
		return $this->getFoldersModel()->getCurrentFolder();
	}

	/**
	 * Build browsable list of files with pagination support
	 *
	 * @return  array
	 *
	 * @since   3.6
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
	 * @param null $fileFilter
	 *
	 * @return self
	 */
	public function setFileFilter($fileFilter = null)
	{
		if ($fileFilter == 'image')
		{
			$this->setState('file_filter', self::IMAGE_FILTER);
		}

		return $this;
	}

	/**
	 * Build browsable list of files
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	protected function loadFiles()
	{
		if (!empty($this->files))
		{
			return $this->files;
		}

		$currentFolder = COM_MEDIA_BASE . '/' . $this->getCurrentFolder();

		if (!file_exists($currentFolder))
		{
			return $this->files;
		}

		if (!file_exists($currentFolder))
		{
			return $this->files;
		}

		$fileList    = JFolder::files($currentFolder, $this->getState('file_filter'));
		$fileHashes  = array();
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
	 * Load the object from a file
	 *
	 * @param   string $file
	 * @param   string $currentFolder
	 * @param   string $fileHashes
	 *
	 * @return  JObject
	 *
	 * @since   3.6
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
		$tmpHash      = md5($currentFolder . '/' . $file);
		$fileHashes[] = $tmpHash;

		return $tmp;
	}

	/**
	 * Load object form stored file
	 *
	 * @param   string $storedFile
	 * @param   string $currentFolder
	 * @param   string $fileHashes
	 *
	 * @return  bool|JObject
	 *
	 * @since   3.6
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
	 * @param   $folder
	 *
	 * @return  mixed
	 *
	 * @since   3.6
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
	 * @param   string $file
	 *
	 * @return  bool
	 *
	 * @since   3.6
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
	 * Return the folders model
	 *
	 * @return  MediaModelFolders
	 *
	 * @since   3.6
	 */
	protected function getFoldersModel()
	{
		return JModelLegacy::getInstance('folders', 'MediaModel');
	}

	/**
	 * Return the file model
	 *
	 * @return  MediaModelFile
	 *
	 * @since   3.6
	 */
	protected function getFileModel()
	{
		return JModelLegacy::getInstance('file', 'MediaModel');
	}
}
