<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

require_once __DIR__ . '/file/adapter.php';
require_once __DIR__ . '/file/type.php';
require_once __DIR__ . '/files.php';

/**
 * Media Component File Model
 *
 * @since  3.6
 */
class MediaModelFile extends JModelLegacy
{
	/**
	 * Numerical database identifier for this file
	 *
	 * @var    int
	 * @since  3.6
	 */
	protected $id = null;

	/**
	 * Properties of a file
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $fileProperties = array();

	/**
	 * File type object
	 *
	 * @var    MediaModelFileTypeInterface
	 * @since  3.6
	 */
	protected $fileType = null;

	/**
	 * File adapter object
	 *
	 * @var    MediaModelFileAdapterInterfaceAdapter
	 * @since  3.6
	 */
	protected $fileAdapter = null;

	/**
	 * Return the database identifier
	 *
	 * @return  int
	 *
	 * @since   3.6
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Load a new file model by path
	 *
	 * @param  string $filePath
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function loadByPath($filePath)
	{
		if (JFile::exists($filePath) == false)
		{
			return false;
		}

		$filePath = realpath($filePath);

		$fileExtension = strtolower(JFile::getExt($filePath));
		$mediaBase     = str_replace(DIRECTORY_SEPARATOR, '/', JPATH_ROOT . '/images/');

		// Base file properties
		$this->fileProperties = array(
			'name'          => basename($filePath),
			'title'         => basename($filePath),
			'path'          => $filePath,
			'path_relative' => str_replace($mediaBase, '', $filePath),
			'extension'     => $fileExtension,
			'size'          => filesize($filePath),
			'icon_32'       => 'mime-icon-32/' . $fileExtension . '.png',
			'icon_16'       => 'mime-icon-16/' . $fileExtension . '.png',
			'file_adapter'  => 'local',
			'file_type'     => 'default',
		);

		// Detect properties per file type
		$this->attachStoredFile($filePath);
		$this->loadFileAdapter();
		$this->loadFileType();
		$this->setPropertiesByFileType();
		$this->setPropertiesByFileAdapter();

		return true;
	}

	/**
	 * Attach a file stored in the database to a filepath-based file
	 *
	 * @param   string $filePath
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	protected function attachStoredFile($filePath)
	{
		// Attach the database stored file to this detected version
		$storedFile = $this->getStoredFileByPath($filePath);

		if (empty($storedFile))
		{
			try
			{
				$this->id = $this->create();
			}
			catch (Exception $e)
			{
				// Do nothing
			}

			$this->fileProperties['id'] = $this->id;

			return true;
		}

		$this->id = $storedFile->id;

		$this->fileProperties['id']           = $this->id;
		$this->fileProperties['hash']         = $storedFile->md5sum;
		$this->fileProperties['file_adapter'] = $storedFile->adapter;

		// Check for hash to see if this entry needs updating
		if (empty($this->fileAdapter))
		{
			return true;
		}

		$this->fileAdapter->setFilePath($this->fileProperties['path']);

		if ($this->fileAdapter->getHash() != $this->fileProperties['hash'])
		{
			try
			{
				$this->update();
			}
			catch (Exception $e)
			{
				// Do nothing
			}
		}

		return true;
	}

	/**
	 * Find a stored file by its filename or path
	 *
	 * @param   string $filePath
	 *
	 * @return  bool|object
	 *
	 * @since   3.6
	 */
	protected function getStoredFileByPath($filePath)
	{
		$path     = str_replace(JPATH_ROOT . '/', '', dirname($filePath));
		$filename = basename($filePath);

		foreach ($this->getStoredFiles($path) as $storedFile)
		{
			if ($storedFile->filename == $filename && $storedFile->path == $path)
			{
				return $storedFile;
			}
		}

		return false;
	}

	/**
	 * Fetch a list of all the files stored in the database
	 *
	 * @param   string $folder
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	protected function getStoredFiles($folder = null)
	{
		static $files = array();

		if (empty($files[$folder]))
		{
			$files[$folder] = $this->getFilesModel()
				->getStoredFiles($folder);
		}

		return $files[$folder];
	}

	/**
	 * Create a new entry for this file in the database
	 *
	 * @return  bool|int
	 * @throw   Exception
	 *
	 * @since   3.6
	 */
	protected function create()
	{
		$user = JFactory::getUser();
		$date = JFactory::getDate();

		if (empty($this->fileProperties))
		{
			return false;
		}

		$path = str_replace(JPATH_ROOT . '/', '', dirname($this->fileProperties['path']));
		$hash = null;

		if ($this->fileAdapter instanceof MediaModelFileAdapterInterfaceAdapter)
		{
			$hash = $this->fileAdapter->getHash();
		}

		$data = array(
			'filename'   => basename($this->fileProperties['path']),
			'path'       => $path,
			'md5sum'     => $hash,
			'user_id'    => $user->id,
			'created_by' => $user->id,
			'created'    => $date->toSql(),
			'adapter'    => 'local',
			'published'  => 1,
			'ordering'   => 1,
		);

		// Get the table
		$table = JTable::getInstance('File', 'MediaTable');

		if (!$table->save($data))
		{
			throw new RuntimeException($table->getError());
		}

		return JFactory::getDbo()->insertid();
	}

	/**
	 * Update the current stored file
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	protected function update()
	{
		$user = JFactory::getUser();
		$date = JFactory::getDate();

		if (empty($this->fileProperties))
		{
			return false;
		}

		$path = str_replace(JPATH_ROOT . '/', '', dirname($this->fileProperties['path']));
		$hash = null;

		if ($this->fileAdapter instanceof MediaModelFileAdapterInterfaceAdapter)
		{
			$hash = $this->fileAdapter->getHash();
		}

		$data = array(
			'id'          => $this->id,
			'filename'    => basename($this->fileProperties['path']),
			'path'        => $path,
			'md5sum'      => $hash,
			'user_id'     => $user->id,
			'modified_by' => $user->id,
			'modified'    => $date->toSql(),
			'adapter'     => 'local',
			'published'   => 1,
			'ordering'    => 1,
		);

		// Get the table 
		$table = JTable::getInstance('File', 'MediaTable');

		if (!$table->save($data))
		{
			throw new RuntimeException($table->getError());
		}

		return $this->id;
	}

	/**
	 * Method to set the current file adapter
	 *
	 * @return  MediaModelFileAdapterInterfaceAdapter
	 *
	 * @since   3.6
	 */
	protected function loadFileAdapter()
	{
		if ($this->fileAdapter instanceof MediaModelFileAdapterInterfaceAdapter)
		{
			return $this->fileAdapter;
		}

		if (!isset($this->fileProperties['file_adapter']))
		{
			return false;
		}

		$adapterFactory    = new MediaModelFileAdapter;
		$this->fileAdapter = $adapterFactory->getFileAdapter($this->fileProperties['file_adapter']);
		$this->fileAdapter->setFilePath($this->fileProperties['path']);

		return $this->fileAdapter;
	}

	/**
	 * Method to detect which file type class to use for a specific $_file
	 *
	 * @return  MediaModelFileAdapterInterfaceAdapter
	 *
	 * @since   3.6
	 */
	protected function loadFileType()
	{
		if ($this->fileType instanceof MediaModelFileTypeInterface)
		{
			return $this->fileType;
		}

		if (!isset($this->fileProperties['path']))
		{
			return false;
		}

		if (!$this->fileAdapter instanceof MediaModelFileAdapterInterfaceAdapter)
		{
			$this->loadFileAdapter();
		}

		$typeFactory    = new MediaModelFileType;
		$this->fileType = $typeFactory->getFileType($this->fileProperties['path'], $this->fileAdapter);

		if (!$this->fileType instanceof MediaModelFileTypeInterface)
		{
			throw new RuntimeException(JText::_('JERROR_UNDEFINED') . ': ' . $this->fileProperties['path']);
		}

		$this->fileProperties['file_type'] = $this->fileType->getName();

		return $this->fileType;
	}

	/**
	 * Merge file type specific properties with the generic file properties
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function setPropertiesByFileType()
	{
		if ($this->fileType)
		{
			$properties           = $this->fileType->getProperties($this->fileProperties['path']);
			$this->fileProperties = array_merge($this->fileProperties, $properties);
		}
	}

	/**
	 * Merge file type specific properties with the generic file properties
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function setPropertiesByFileAdapter()
	{
		if (!$this->fileAdapter)
		{
			return;
		}

		$mimeType = $this->fileAdapter->getMimeType($this->fileProperties['path']);

		if (empty($mimeType))
		{
			return;
		}
		
		$this->fileProperties['mime_type'] = $mimeType;
	}

	/**
	 * Return the current file adapter object
	 *
	 * @return  mixed
	 *
	 * @since   3.6
	 */
	public function getFileAdapter()
	{
		return $this->fileAdapter;
	}

	/**
	 * Set the current file adapter object
	 *
	 * @param  string $fileAdapter
	 * @param  string $filePath
	 *
	 * @return  $this
	 *
	 * @since   3.6
	 */
	public function setFileAdapter($fileAdapterName, $filePath = null)
	{
		$adapterFactory    = new MediaModelFileAdapter;
		$this->fileAdapter = $adapterFactory->getFileAdapter($fileAdapterName);
		$this->fileAdapter->setFilePath($filePath);

		return $this;
	}

	/**
	 * Return the current file type object
	 *
	 * @return  mixed
	 *
	 * @since   3.6
	 */
	public function getFileType()
	{
		return $this->fileType;
	}

	/**
	 * Set the current file type object
	 *
	 * @param   mixed $fileType
	 *
	 * @return  $this
	 *
	 * @since   3.6
	 */
	public function setFileType($fileType)
	{
		$this->fileType = $fileType;

		return $this;
	}

	/**
	 * Get the file properties
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getFileProperties()
	{
		return $this->fileProperties;
	}

	/**
	 * Set the file properties
	 *
	 * @param   array $properties
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	public function setFileProperties($properties)
	{
		$this->fileProperties = $properties;
	}

	/**
	 * Return th files model
	 *
	 * @return  MediaModelFiles
	 *
	 * @since   3.6
	 */
	public function getFilesModel()
	{
		return JModelLegacy::getInstance('files', 'MediaModel');
	}
}
