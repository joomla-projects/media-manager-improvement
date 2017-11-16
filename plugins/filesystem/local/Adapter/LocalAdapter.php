<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Filesystem.Local
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\Filesystem\Local\Adapter;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\Component\Media\Administrator\Adapter\AdapterInterface;
use Joomla\Component\Media\Administrator\Exception\FileNotFoundException;
use Joomla\Image\Image;
use Joomla\CMS\Uri\Uri;

\JLoader::import('joomla.filesystem.file');
\JLoader::import('joomla.filesystem.folder');

/**
 * Local file adapter.
 *
 * @since  __DEPLOY_VERSION__
 */
class LocalAdapter implements AdapterInterface
{
	/**
	 * The root path to gather file information from.
	 *
	 * @var string
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $rootPath = null;

	/**
	 * The file_path of media directory related to site
	 *
	 * @var string
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private $filePath = null;

	/**
	 * The absolute root path in the local file system.
	 *
	 * @param   string  $rootPath  The root path
	 * @param   string  $filePath  The file path of media folder
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($rootPath, $filePath)
	{
		if (!file_exists($rootPath))
		{
			throw new \InvalidArgumentException;
		}

		$this->rootPath = \JPath::clean($rootPath, '/');
		$this->filePath = $filePath;
	}

	/**
	 * Returns the requested file or folder. The returned object
	 * has the following properties available:
	 * - type:          The type can be file or dir
	 * - name:          The name of the file
	 * - path:          The relative path to the root
	 * - extension:     The file extension
	 * - size:          The size of the file
	 * - create_date:   The date created
	 * - modified_date: The date modified
	 * - mime_type:     The mime type
	 * - width:         The width, when available
	 * - height:        The height, when available
	 *
	 * If the path doesn't exist a FileNotFoundException is thrown.
	 *
	 * @param   string  $path  The path to the file or folder
	 *
	 * @return  \stdClass
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function getFile($path = '/')
	{
		// Set up the path correctly
		$basePath = \JPath::clean($this->rootPath . '/' . $path);

		// Check if file exists
		if (!file_exists($basePath))
		{
			throw new FileNotFoundException;
		}

		return $this->getPathInformation($basePath);
	}

	/**
	 * Returns the folders and files for the given path. The returned objects
	 * have the following properties available:
	 * - type:          The type can be file or dir
	 * - name:          The name of the file
	 * - path:          The relative path to the root
	 * - extension:     The file extension
	 * - size:          The size of the file
	 * - create_date:   The date created
	 * - modified_date: The date modified
	 * - mime_type:     The mime type
	 * - width:         The width, when available
	 * - height:        The height, when available
	 *
	 * If the path doesn't exist a FileNotFoundException is thrown.
	 *
	 * @param   string  $path  The folder
	 *
	 * @return  \stdClass[]
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function getFiles($path = '/')
	{
		// Set up the path correctly
		$basePath = \JPath::clean($this->rootPath . '/' . $path);

		// Check if file exists
		if (!file_exists($basePath))
		{
			throw new FileNotFoundException;
		}

		// Check if the path points to a file
		if (is_file($basePath))
		{
			return array($this->getPathInformation($basePath));
		}

		// The data to return
		$data = array();

		// Read the folders
		foreach (\JFolder::folders($basePath) as $folder)
		{
			$data[] = $this->getPathInformation(\JPath::clean($basePath . '/' . $folder));
		}

		// Read the files
		foreach (\JFolder::files($basePath) as $file)
		{
			$data[] = $this->getPathInformation(\JPath::clean($basePath . '/' . $file));
		}

		// Return the data
		return $data;
	}

	/**
	 * Returns a resource to download the path.
	 *
	 * @param   string  $path  The path to download
	 *
	 * @return  resource
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function getResource($path)
	{
		return fopen($this->rootPath . '/' . $path, 'r');
	}

	/**
	 * Creates a folder with the given name in the given path.
	 *
	 * It returns the new folder name. This allows the implementation
	 * classes to normalise the file name.
	 *
	 * @param   string  $name  The name
	 * @param   string  $path  The folder
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function createFolder($name, $path)
	{
		$name = $this->getSafeName($name);

		\JFolder::create($this->rootPath . $path . '/' . $name);

		return $name;
	}

	/**
	 * Creates a file with the given name in the given path with the data.
	 *
	 * It returns the new file name. This allows the implementation
	 * classes to normalise the file name.
	 *
	 * @param   string  $name  The name
	 * @param   string  $path  The folder
	 * @param   binary  $data  The data
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function createFile($name, $path, $data)
	{
		$name = $this->getSafeName($name);

		$this->checkContent($name, $path, $data);

		\JFile::write($this->rootPath . $path . '/' . $name, $data);

		return $name;
	}

	/**
	 * Updates the file with the given name in the given path with the data.
	 *
	 * @param   string  $name  The name
	 * @param   string  $path  The folder
	 * @param   binary  $data  The data
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function updateFile($name, $path, $data)
	{
		if (!\JFile::exists($this->rootPath . $path . '/' . $name))
		{
			throw new FileNotFoundException;
		}

		$this->checkContent($name, $path, $data);

		\JFile::write($this->rootPath . $path . '/' . $name, $data);
	}


	/**
	 * Deletes the folder or file of the given path.
	 *
	 * @param   string  $path  The path to the file or folder
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function delete($path)
	{
		if (is_file($this->rootPath . $path))
		{
			if (!\JFile::exists($this->rootPath . $path))
			{
				throw new FileNotFoundException;
			}

			$success = \JFile::delete($this->rootPath . $path);
		}
		else
		{
			if (!\JFolder::exists($this->rootPath . $path))
			{
				throw new FileNotFoundException;
			}

			$success = \JFolder::delete($this->rootPath . $path);
		}

		if (!$success)
		{
			throw new \Exception('Delete not possible!');
		}
	}

	/**
	 * Returns the folder or file information for the given path. The returned object
	 * has the following properties:
	 * - type:          The type can be file or dir
	 * - name:          The name of the file
	 * - path:          The relative path to the root
	 * - extension:     The file extension
	 * - size:          The size of the file
	 * - create_date:   The date created
	 * - modified_date: The date modified
	 * - mime_type:     The mime type
	 * - width:         The width, when available
	 * - height:        The height, when available
	 * - thumb_path     The thumbnail path of file, when available
	 *
	 * @param   string  $path  The folder
	 *
	 * @return  \stdClass
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function getPathInformation($path)
	{
		// Prepare the path
		$path = \JPath::clean($path, '/');

		// The boolean if it is a dir
		$isDir = is_dir($path);

		$createDate   = $this->getDate(filectime($path));
		$modifiedDate = $this->getDate(filemtime($path));

		// Set the values
		$obj            = new \stdClass;
		$obj->type      = $isDir ? 'dir' : 'file';
		$obj->name      = $this->getFileName($path);
		$obj->path      = str_replace($this->rootPath, '/', $path);
		$obj->localpath = $path;
		$obj->extension = !$isDir ? \JFile::getExt($obj->name) : '';
		$obj->size      = !$isDir ? filesize($path) : 0;
		$obj->mime_type = MediaHelper::getMimeType($path, MediaHelper::isImage($obj->name));
		$obj->width     = 0;
		$obj->height    = 0;

		// Dates
		$obj->create_date             = $createDate->format('c', true);
		$obj->create_date_formatted   = $createDate->format(\JText::_('DATE_FORMAT_LC5'), true);
		$obj->modified_date           = $modifiedDate->format('c', true);
		$obj->modified_date_formatted = $modifiedDate->format(\JText::_('DATE_FORMAT_LC5'), true);

		if (MediaHelper::isImage($obj->name))
		{
			// Get the image properties
			$props       = Image::getImageFileProperties($path);
			$obj->width  = $props->width;
			$obj->height = $props->height;

			// Todo : Change this path to an actual thumbnail path
			$obj->thumb_path = $this->getUrl($obj->path);
		}

		return $obj;
	}

	/**
	 * Returns a JDate with the correct Joomla timezone for the given date.
	 *
	 * @param   string  $date  The date to create a JDate from
	 *
	 * @return  Date[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function getDate($date = null)
	{
		$dateObj = \JFactory::getDate($date);

		$timezone = \JFactory::getApplication()->get('offset');
		$user     = \JFactory::getUser();

		if ($user->id)
		{
			$userTimezone = $user->getParam('timezone');
			if (!empty($userTimezone))
			{
				$timezone = $userTimezone;
			}
		}

		if ($timezone)
		{
			$dateObj->setTimezone(new \DateTimeZone($timezone));
		}

		return $dateObj;
	}

	/**
	 * Copies a file or folder from source to destination.
	 *
	 * It returns the new destination path. This allows the implementation
	 * classes to normalise the file name.
	 *
	 * @param   string  $sourcePath       The source path
	 * @param   string  $destinationPath  The destination path
	 * @param   bool    $force            Force to overwrite
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function copy($sourcePath, $destinationPath, $force = false)
	{
		// Get absolute paths from relative paths
		$sourcePath      = \JPath::clean($this->rootPath . $sourcePath, '/');
		$destinationPath = \JPath::clean($this->rootPath . $destinationPath, '/');

		if (!file_exists($sourcePath))
		{
			throw new FileNotFoundException;
		}

		$name     = $this->getFileName($destinationPath);
		$safeName = $this->getSafeName($name);

		// If the safe name is different normalise the file name
		if ($safeName != $name)
		{
			$destinationPath = substr($destinationPath, 0, -strlen($name)) . '/' . $safeName;
		}

		// Check for existence of the file in destination
		// if it does not exists simply copy source to destination
		if (is_dir($sourcePath))
		{
			$this->copyFolder($sourcePath, $destinationPath, $force);
		}
		else
		{
			$this->copyFile($sourcePath, $destinationPath, $force);
		}

		return $destinationPath;
	}

	/**
	 * Copies a file
	 *
	 * @param   string  $sourcePath       Source path of the file or directory
	 * @param   string  $destinationPath  Destination path of the file or directory
	 * @param   bool    $force            Set true to overwrite files or directories
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	private function copyFile($sourcePath, $destinationPath, $force = false)
	{
		if (is_dir($destinationPath))
		{
			// If the destination is a folder we create a file with the same name as the source
			$destinationPath = $destinationPath . '/' . $this->getFileName($sourcePath);
		}

		if (file_exists($destinationPath) && !$force)
		{
			throw new \Exception('Copy file is not possible as destination file already exists');
		}

		if (!\JFile::copy($sourcePath, $destinationPath))
		{
			throw new \Exception('Copy file is not possible');
		}
	}

	/**
	 * Copies a folder
	 *
	 * @param   string  $sourcePath       Source path of the file or directory
	 * @param   string  $destinationPath  Destination path of the file or directory
	 * @param   bool    $force            Set true to overwrite files or directories
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	private function copyFolder($sourcePath, $destinationPath, $force = false)
	{
		if (file_exists($destinationPath) && !$force)
		{
			throw new \Exception('Copy folder is not possible as destination folder already exists');
		}

		if (is_file($destinationPath) && !\JFile::delete($destinationPath))
		{
			throw new \Exception('Copy folder is not possible as destination folder is a file and can not be deleted');
		}

		if (!\JFolder::copy($sourcePath, $destinationPath, '', $force))
		{
			throw new \Exception('Copy folder is not possible');
		}
	}

	/**
	 * Moves a file or folder from source to destination.
	 *
	 * It returns the new destination path. This allows the implementation
	 * classes to normalise the file name.
	 *
	 * @param   string  $sourcePath       The source path
	 * @param   string  $destinationPath  The destination path
	 * @param   bool    $force            Force to overwrite
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function move($sourcePath, $destinationPath, $force = false)
	{
		// Get absolute paths from relative paths
		$sourcePath      = \JPath::clean($this->rootPath . $sourcePath, '/');
		$destinationPath = \JPath::clean($this->rootPath . $destinationPath, '/');

		if (!file_exists($sourcePath))
		{
			throw new FileNotFoundException;
		}

		$name     = $this->getFileName($destinationPath);
		$safeName = $this->getSafeName($name);

		// If the safe name is different normalise the file name
		if ($safeName != $name)
		{
			$destinationPath = substr($destinationPath, 0, -strlen($name)) . '/' . $safeName;
		}

		if (is_dir($sourcePath))
		{
			$this->moveFolder($sourcePath, $destinationPath, $force);
		}
		else
		{
			$this->moveFile($sourcePath, $destinationPath, $force);
		}

		return $destinationPath;
	}

	/**
	 * Moves a file
	 *
	 * @param   string  $sourcePath       Absolute path of source
	 * @param   string  $destinationPath  Absolute path of destination
	 * @param   bool    $force            Set true to overwrite file if exists
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	private function moveFile($sourcePath, $destinationPath, $force = false)
	{
		if (is_dir($destinationPath))
		{
			// If the destination is a folder we create a file with the same name as the source
			$destinationPath = $destinationPath . '/' . $this->getFileName($sourcePath);
		}

		if (file_exists($destinationPath) && !$force)
		{
			throw new \Exception('Move file is not possible as destination file already exists');
		}

		if (!\JFile::move($sourcePath, $destinationPath))
		{
			throw new \Exception('Move file is not possible');
		}
	}

	/**
	 * Moves a folder from source to destination
	 *
	 * @param   string  $sourcePath       Source path of the file or directory
	 * @param   string  $destinationPath  Destination path of the file or directory
	 * @param   bool    $force            Set true to overwrite files or directories
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	private function moveFolder($sourcePath, $destinationPath, $force = false)
	{
		if (file_exists($destinationPath) && !$force)
		{
			throw new \Exception('Move folder is not possible as destination folder already exists');
		}

		if (is_file($destinationPath) && !\JFile::delete($destinationPath))
		{
			throw new \Exception('Move folder is not possible as destination folder is a file and can not be deleted');
		}

		if (is_dir($destinationPath))
		{
			// We need to bypass exception thrown in JFolder when destination exists
			// So we only copy it in forced condition, then delete the source to simulate a move
			if (!\JFolder::copy($sourcePath, $destinationPath, '', true))
			{
				throw new \Exception('Move folder to an existing destination failed');
			}

			// Delete the source
			\JFolder::delete($sourcePath);

			return;
		}

		// Perform usual moves
		$value = \JFolder::move($sourcePath, $destinationPath);

		if ($value !== true)
		{
			throw new \Exception($value);
		}
	}

	/**
	 * Returns an url which can be used to display an image from within the "images" directory.
	 *
	 * @param   string  $path  Path of the file relative to adapter
	 *
	 * @return string
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function getUrl($path)
	{
		return Uri::root() . $this->getEncodedPath($this->filePath . $path);
	}

	/**
	 * Returns the name of this adapter.
	 *
	 * @return string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getAdapterName()
	{
		return $this->filePath;
	}

	/**
	 * Search for a pattern in a given path
	 *
	 * @param   string  $path       The base path for the search
	 * @param   string  $needle     The path to file
	 * @param   bool    $recursive  Do a recursive search
	 *
	 * @return \stdClass[]
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function search($path, $needle, $recursive)
	{
		$pattern = \JPath::clean($this->rootPath . '/' . $path . '/*' . $needle . '*');

		if ($recursive)
		{
			$results = $this->rglob($pattern);
		}
		else
		{
			$results = glob($pattern);
		}

		$searchResults = [];

		foreach ($results as $result)
		{
			$searchResults[] = $this->getPathInformation($result);
		}

		return $searchResults;
	}

	/**
	 * Do a recursive search on a given path
	 *
	 * @param   string  $pattern  The pattern for search
	 * @param   int     $flags    Flags for search
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function rglob($pattern, $flags = 0)
	{
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
		{
			$files = array_merge($files, $this->rglob($dir . '/' . $this->getFileName($pattern), $flags));
		}

		return $files;
	}

	/**
	 * Returns a temporary url for the given path.
	 * This is used internally in media manager
	 *
	 * @param   string  $path  The path to file
	 *
	 * @return string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws FileNotFoundException
	 */
	public function getTemporaryUrl($path)
	{
		return $this->getUrl($path);
	}

	/**
	 * Replace spaces on a path with %20
	 *
	 * @param   string  $path  The Path to be encoded
	 *
	 * @return string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws FileNotFoundException
	 */
	private function getEncodedPath($path)
	{
		return str_replace(" ", "%20", $path);
	}

	/**
	 * Creates a safe file name for the given name.
	 *
	 * @param   string  $name  The filename
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	private function getSafeName($name)
	{
		// Make the filename safe
		$name = \JFile::makeSafe($name);

		// Transform filename to punycode
		$name = PunycodeHelper::toPunycode($name);

		// Get the extension
		$extension = \JFile::getExt($name);

		// Normalise extension, always lower case
		if ($extension)
		{
			$extension = '.' . strtolower($extension);
		}

		$nameWithoutExtension = substr($name, 0, strlen($name) - strlen($extension));

		return $nameWithoutExtension . $extension;
	}

	/**
	 * Performs various check if it is allowed to save the content with the given name.
	 *
	 * @param   string  $name          The filename
	 * @param   string  $path          The path
	 * @param   string  $mediaContent  The media content
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	private function checkContent($name, $path, $mediaContent)
	{
		// The helper
		$helper = new MediaHelper;

		// @todo find a better way to check the input, by not writing the file to the disk
		$tmpFile = \JPath::clean($this->rootPath . '/' . $path . '/' . uniqid() . '.' . \JFile::getExt($name));

		if (!\JFile::write($tmpFile, $mediaContent))
		{
			throw new \Exception(\JText::_('JLIB_MEDIA_ERROR_UPLOAD_INPUT'), 500);
		}

		$can = $helper->canUpload(array('name' => $name, 'size' => count($mediaContent), 'tmp_name' => $tmpFile), 'com_media');

		\JFile::delete($tmpFile);

		if (!$can)
		{
			throw new \Exception(\JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'), 403);
		}
	}

	/**
	 * Returns the file name of the given path.
	 *
	 * @param   string  $path The path
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	private function getFileName($path)
	{
		// Basename does not work here as it strips out certain characters like upper case umlaut u
		$path = explode(DIRECTORY_SEPARATOR, $path);

		// Return the last element
		return array_pop($path);
	}
}
