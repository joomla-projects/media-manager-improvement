<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Mvc\Factory\MvcFactoryInterface;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Media\Administrator\Adapter\AdapterInterface;
use Joomla\Component\Media\Administrator\Adapter\AdapterManager;
use Joomla\Component\Media\Administrator\Adapter\FileNotFoundException;
use Joomla\Component\Media\Administrator\Event\MediaAdapterEvent;

/**
 * Api Model
 *
 * @since  __DEPLOY_VERSION__
 */
class Api extends BaseModel
{
	/**
	 * Holds available media file adapters.
	 *
	 * @var   AdapterManager
	 * @since  __DEPLOY_VERSION__
	 */
	protected $adapterManager = null;

	/**
	 * Constructor
	 *
	 * @param   array                $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   MvcFactoryInterface  $factory  The factory.
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function __construct($config = array(), MvcFactoryInterface $factory = null)
	{
		parent::__construct($config, $factory);

		// Setup adapters
		$this->setupAdapters();
	}

	/**
	 * Setup the adapters for Media Manager
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private function setupAdapters()
	{
		// Get the providers
		$providers = PluginHelper::getPlugin('filesystem');

		// Fire the event to get the results
		PluginHelper::importPlugin('filesystem');
		$eventParameters = ['context' => 'AdapterManager'];
		$event = new MediaAdapterEvent('onSetupAdapterManager', $eventParameters);
		$results = (array) Factory::getApplication()->triggerEvent('onSetupAdapterManager', $event);

		$adapters = array();

		for ($i = 0, $len = count($results); $i < $len; $i++)
		{
			$adapters[$providers[$i]->name] = $results[$i];
		}

		$this->adapterManager = new AdapterManager($adapters);
	}

	/**
	 * Return the requested adapter
	 *
	 * @param   string  $name  Name of the provider
	 *
	 * @since   __DEPLOY_VERSION__
	 * @return AdapterInterface
	 *
	 * @throws \Exception
	 */
	private function getAdapter($name)
	{
		list($adapter, $account) = array_pad(explode('-', $name, 2), 2, null);

		if ($account == null)
		{
			throw new \Exception('Account was not set');
		}

		$adapters = $this->adapterManager->getAdapters();

		if (isset($adapters[$adapter][$account]))
		{
			return $adapters[$adapter][$account];
		}

		// Todo Use a translated string
		throw new \InvalidArgumentException('Requested media file adapter was not found', 500);
	}

	/**
	 * Returns the requested file or folder information. More information
	 * can be found in AdapterInterface::getFile().
	 *
	 * @param   string  $adapter  The adapter
	 * @param   string  $path     The path to the file or folder
	 * @param   array   $options  The options
	 *
	 * @return  \stdClass
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 * @see     AdapterInterface::getFile()
	 */
	public function getFile($adapter, $path = '/', $options = array())
	{
		// Add adapter prefix to the file returned
		$file = $this->getAdapter($adapter)->getFile($path);

		if (isset($options['url']) && $options['url'] && $file->type == 'file')
		{
			$file->url = $this->getUrl($adapter, $file->path);
		}

		$file->path    = $adapter . ":" . $file->path;
		$file->adapter = $adapter;

		return $file;
	}

	/**
	 * Returns the folders and files for the given path. More information
	 * can be found in AdapterInterface::getFiles().
	 *
	 * @param   string  $adapter  The adapter
	 * @param   string  $path     The folder
	 * @param   string  $filter   The filter
	 * @param   array   $options  The options
	 *
	 * @return  \stdClass[]
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 * @see     AdapterInterface::getFile()
	 */
	public function getFiles($adapter, $path = '/', $filter = '', $options = array())
	{
		// Add adapter prefix to all the files to be returned
		$files = $this->getAdapter($adapter)->getFiles($path, $filter);

		foreach ($files as $file)
		{
			// If requested add options
			// Url can be provided for a file
			if (isset($options['url']) && $options['url'] && $file->type == 'file')
			{
				$file->url = $this->getUrl($adapter, $file->path);
			}

			$file->path    = $adapter . ":" . $file->path;
			$file->adapter = $adapter;
		}

		return $files;
	}

	/**
	 * Creates a folder with the given name in the given path. More information
	 * can be found in AdapterInterface::createFolder().
	 *
	 * @param   string  $adapter  The adapter
	 * @param   string  $name     The name
	 * @param   string  $path     The folder
	 *
	 * @return  string  The new file name
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 * @see     AdapterInterface::createFolder()
	 */
	public function createFolder($adapter, $name, $path)
	{
		$this->getAdapter($adapter)->createFolder($name, $path);

		return $name;
	}

	/**
	 * Creates a file with the given name in the given path with the data. More information
	 * can be found in AdapterInterface::createFile().
	 *
	 * @param   string  $adapter  The adapter
	 * @param   string  $name     The name
	 * @param   string  $path     The folder
	 * @param   binary  $data     The data
	 *
	 * @return  string  The new file name
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 * @see     AdapterInterface::createFile()
	 */
	public function createFile($adapter, $name, $path, $data)
	{
		$this->getAdapter($adapter)->createFile($name, $path, $data);

		return $name;
	}

	/**
	 * Updates the file with the given name in the given path with the data. More information
	 * can be found in AdapterInterface::updateFile().
	 *
	 * @param   string  $adapter  The adapter
	 * @param   string  $name     The name
	 * @param   string  $path     The folder
	 * @param   binary  $data     The data
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 * @see     AdapterInterface::updateFile()
	 */
	public function updateFile($adapter, $name, $path, $data)
	{
		$this->getAdapter($adapter)->updateFile($name, $path, $data);
	}

	/**
	 * Deletes the folder or file of the given path. More information
	 * can be found in AdapterInterface::delete().
	 *
	 * @param   string  $adapter  The adapter
	 * @param   string  $path     The path to the file or folder
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 * @see     AdapterInterface::delete()
	 */
	public function delete($adapter, $path)
	{
		$this->getAdapter($adapter)->delete($path);
	}

	/**
	 * Copies file or folder from source path to destination path
	 * If forced, existing files/folders would be overwritten
	 *
	 * @param   string  $adapter          The adapter
	 * @param   string  $sourcePath       Source path of the file or folder (relative)
	 * @param   string  $destinationPath  Destination path(relative)
	 * @param   bool    $force            Force to overwrite
	 *
	 * @return void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function copy($adapter, $sourcePath, $destinationPath, $force = false)
	{
		$this->getAdapter($adapter)->copy($sourcePath, $destinationPath, $force);
	}

	/**
	 * Moves file or folder from source path to destination path
	 * If forced, existing files/folders would be overwritten
	 *
	 * @param   string  $adapter          The adapter
	 * @param   string  $sourcePath       Source path of the file or folder (relative)
	 * @param   string  $destinationPath  Destination path(relative)
	 * @param   bool    $force            Force to overwrite
	 *
	 * @return void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function move($adapter, $sourcePath, $destinationPath, $force = false)
	{
		$this->getAdapter($adapter)->move($sourcePath, $destinationPath, $force);
	}

	/**
	 * Returns an url for serve media files from adapter.
	 * Url must provide a valid image type to be displayed on Joomla! site.
	 *
	 * @param   string  $adapter  The adapter
	 * @param   string  $path     The relative path for the file
	 *
	 * @return string  Permalink to the relative file
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws FileNotFoundException
	 */
	public function getUrl($adapter, $path)
	{
		return $this->getAdapter($adapter)->getUrl($path);
	}
}
