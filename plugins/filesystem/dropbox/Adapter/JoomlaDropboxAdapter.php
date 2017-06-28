<?php
/**
 * Created by PhpStorm.
 * User: kasun
 * Date: 6/27/17
 * Time: 11:16 PM
 */

namespace Joomla\Plugin\Filesystem\Dropbox\Adapter;

defined('_JEXEC') or die;

\JLoader::import('filesystem.dropbox.vendor.autoload', JPATH_PLUGINS);

use Joomla\Component\Media\Administrator\Adapter\AdapterInterface;
use Joomla\Component\Media\Administrator\Adapter\FileNotFoundException;

class JoomlaDropboxAdapter implements AdapterInterface
{
	private $client = null;
	private $adapter = null;
	private $filesystem = null;

	/**
	 * DropboxAdapter constructor.
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($apiToken)
	{
		$this->client = new \Srmklive\Dropbox\Client\DropboxClient($apiToken);

		$this->adapter = new \Srmklive\Dropbox\Adapter\DropboxAdapter($this->client);

		$this->filesystem = new \League\Flysystem\Filesystem($this->adapter);
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
	 * @param   string $path The path to the file or folder
	 *
	 * @return  \stdClass[]
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function getFile( $path = '/' )
	{
		// TODO: Implement getFile() method.
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
	 * @param   string $path   The folder
	 * @param   string $filter The filter
	 *
	 * @return  \stdClass[]
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function getFiles( $path = '/', $filter = '' )
	{
		// TODO: Implement getFiles() method.
	}

	/**
	 * Creates a folder with the given name in the given path.
	 *
	 * @param   string $name The name
	 * @param   string $path The folder
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function createFolder( $name, $path )
	{
		// TODO: Implement createFolder() method.
	}

	/**
	 * Creates a file with the given name in the given path with the data.
	 *
	 * @param   string $name The name
	 * @param   string $path The folder
	 * @param   binary $data The data
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function createFile( $name, $path, $data )
	{
		// TODO: Implement createFile() method.
	}

	/**
	 * Updates the file with the given name in the given path with the data.
	 *
	 * @param   string $name The name
	 * @param   string $path The folder
	 * @param   binary $data The data
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function updateFile( $name, $path, $data )
	{
		// TODO: Implement updateFile() method.
	}

	/**
	 * Deletes the folder or file of the given path.
	 *
	 * @param   string $path The path to the file or folder
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function delete( $path )
	{
		// TODO: Implement delete() method.
	}

	/**
	 * Moves a file or folder from source to destination
	 *
	 * @param   string $sourcePath      The source path
	 * @param   string $destinationPath The destination path
	 * @param   bool   $force           Force to overwrite
	 *
	 * @return void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function move( $sourcePath, $destinationPath, $force = false )
	{
		// TODO: Implement move() method.
	}

	/**
	 * Copies a file or folder from source to destination
	 *
	 * @param   string $sourcePath      The source path
	 * @param   string $destinationPath The destination path
	 * @param   bool   $force           Force to overwrite
	 *
	 * @return void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  \Exception
	 */
	public function copy( $sourcePath, $destinationPath, $force = false )
	{
		// TODO: Implement copy() method.
	}

	/**
	 * Returns a permanent link for media file.
	 *
	 * @param   string $path The path to file
	 *
	 * @return mixed
	 * @since   __DEPLOY_VERSION__
	 * @throws FileNotFoundException
	 */
	public function getPermalink( $path )
	{
		// TODO: Implement getPermalink() method.
	}

}