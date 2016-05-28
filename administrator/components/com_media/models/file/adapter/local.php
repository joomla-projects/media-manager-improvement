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
 * Media Manager model to abstract the usage of local file actions
 *
 * @since  3.6
 */
class MediaModelFileAdapterLocal extends MediaModelFileAdapterAbstract implements MediaModelFileAdapterInterfaceAdapter, MediaModelFileAdapterInterfaceFlysystem
{
	/**
	 * @var    int
	 * @since  3.6
	 */
	const SKIP_LINKS = 0001;
	/**
	 * @var    int
	 * @since  3.6
	 */
	const DISALLOW_LINKS = 0002;

	/**
	 * An array with the file and folder permissions
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected static $permissions = [
		'file' => [
			'public'  => 0644,
			'private' => 0600,
		],
		'dir'  => [
			'public'  => 0755,
			'private' => 0700,
		],
	];

	/**
	 * @var    string
	 * @since  3.6
	 */
	protected $pathSeparator = DIRECTORY_SEPARATOR;

	/**
	 * @var    array
	 * @since  3.6
	 */
	protected $permissionMap;

	/**
	 * @var    int
	 * @since  3.6
	 */
	protected $writeFlags;

	/**
	 * @var    int
	 * @since  3.6
	 */
	private $linkHandling;

	/**
	 * Path prefix
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $pathPrefix;

	/**
	 * Constructor.
	 *
	 * @param string $root
	 * @param int    $writeFlags
	 * @param int    $linkHandling
	 * @param array  $permissions
	 *
	 * @since  3.6
	 */
	public function __construct($root, $writeFlags = LOCK_EX, $linkHandling = self::DISALLOW_LINKS, array $permissions = [])
	{
		// The $permissionMap needs to be set before ensureDirectory() is called.
		$this->permissionMap = array_replace_recursive(static::$permissions, $permissions);
		$realRoot            = $this->ensureDirectory($root);

		if (!is_dir($realRoot) || !is_readable($realRoot))
		{
			throw new \LogicException('The root path ' . $root . ' is not readable.');
		}

		$this->setPathPrefix($realRoot);

		$this->writeFlags   = $writeFlags;
		$this->linkHandling = $linkHandling;
	}

	/**
	 * Read the path
	 *
	 * @param   $path
	 *
	 * @return  array|bool
	 *
	 * @since   3.6
	 */
	public function read($path)
	{
		$location = $this->applyPathPrefix($path);
		$contents = file_get_contents($location);

		if ($contents === false)
		{
			return false;
		}

		return compact('contents', 'path');
	}

	/**
	 * Write a new file.
	 *
	 * @param  string $path
	 * @param  string $contents
	 *
	 * @return  array|false  false on failure file meta data on success
	 * @since   3.6
	 */
	public function write($path, $contents)
	{
		$location = $this->applyPathPrefix($path);
		$this->ensureDirectory(dirname($location));

		if (($size = file_put_contents($location, $contents, $this->writeFlags)) === false)
		{
			return false;
		}

		$type   = 'file';
		$result = compact('contents', 'type', 'size', 'path');

		return $result;
	}

	/**
	 * Update a file.
	 *
	 * @param  string $path
	 * @param  string $contents
	 *
	 * @return  array|false false on failure file meta data on success
	 *
	 * @since   3.6
	 */
	public function update($path, $contents)
	{
		$location = $this->applyPathPrefix($path);
		$mimetype = $this->getMimeType($path);
		$size     = file_put_contents($location, $contents, $this->writeFlags);

		if ($size === false)
		{
			return false;
		}

		return compact('path', 'size', 'contents', 'mimetype');
	}

	/**
	 * Rename a file.
	 *
	 * @param  string $path
	 * @param  string $newpath
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function rename($path, $newpath)
	{
		$location        = $this->applyPathPrefix($path);
		$destination     = $this->applyPathPrefix($newpath);
		$parentDirectory = $this->applyPathPrefix(dirname($newpath));

		$this->ensureDirectory($parentDirectory);

		return rename($location, $destination);
	}

	/**
	 * Copy a file.
	 *
	 * @param  string $path
	 * @param  string $newpath
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function copy($path, $newpath)
	{
		$location    = $this->applyPathPrefix($path);
		$destination = $this->applyPathPrefix($newpath);

		$this->ensureDirectory(dirname($destination));

		return copy($location, $destination);
	}

	/**
	 * Delete a file.
	 *
	 * @param   string $path
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function delete($path)
	{
		$location = $this->applyPathPrefix($path);

		return unlink($location);
	}

	/**
	 * Delete a directory.
	 *
	 * @param   string $dirname
	 *
	 * @return  bool
	 *
	 * @since   3.6
	 */
	public function deleteDir($dirname)
	{
		$location = $this->applyPathPrefix($dirname);

		if (!is_dir($location))
		{
			return false;
		}

		$contents = scandir($dirname);

		/** @var SplFileInfo $file */
		foreach ($contents as $file)
		{
			$file = $dirname . '/' . $file;

			if (is_dir($file))
			{
				rmdir($file);
				continue;
			}

			unlink($file);
		}

		return rmdir($location);
	}

	/**
	 * Create a directory.
	 *
	 * @param   string $dirname directory name
	 *
	 * @return  array|false
	 *
	 * @since   3.6
	 */
	public function createDir($dirname)
	{
		$location = $this->applyPathPrefix($dirname);
		$umask    = umask(0);

		umask($umask);

		if (!is_dir($location) && !mkdir($location, $this->permissionMap['dir']['public'], true))
		{
			return false;
		}

		return ['path' => $dirname, 'type' => 'dir'];
	}

	/**
	 * Set the visibility for a file.
	 *
	 * @param   string $path
	 * @param   string $visibility
	 *
	 * @return  array|false file meta data
	 *
	 * @since   3.6
	 */
	public function setVisibility($path, $visibility)
	{
		$location = $this->applyPathPrefix($path);
		$type     = is_dir($location) ? 'dir' : 'file';
		$success  = chmod($location, $this->permissionMap[$type][$visibility]);

		if ($success === false)
		{
			return false;
		}

		return compact('visibility');
	}

	/**
	 * Ensure the root directory exists.
	 *
	 * @param   string $root Root directory path
	 *
	 * @return  string  Real path to root
	 *
	 * @since   3.6
	 */
	protected function ensureDirectory($root)
	{
		if (!is_dir($root))
		{
			$umask = umask(0);
			mkdir($root, $this->permissionMap['dir']['public'], true);
			umask($umask);
		}

		return realpath($root);
	}

	/**
	 * Apply the path prefix to a given path
	 *
	 * @param   string $path
	 *
	 * @return  mixed
	 *
	 * @since   3.6
	 */
	public function applyPathPrefix($path)
	{
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * Set the path prefix.
	 *
	 * @param   string $prefix
	 *
	 * @return  voild
	 *
	 * @since   3.6
	 */
	public function setPathPrefix($prefix)
	{
		$is_empty = empty($prefix);

		if (!$is_empty)
		{
			$prefix = rtrim($prefix, $this->pathSeparator) . $this->pathSeparator;
		}

		$this->pathPrefix = $is_empty ? null : $prefix;
	}

	/**
	 * Get the path prefix.
	 *
	 * @return  string  The path prefix
	 *
	 * @since   3.6
	 */
	public function getPathPrefix()
	{
		return $this->pathPrefix;
	}

	/**
	 * Remove a path prefix.
	 *
	 * @param   string $path
	 *
	 * @return  string  path without the prefix
	 *
	 * @since   3.6
	 */
	public function removePathPrefix($path)
	{
		$pathPrefix = $this->getPathPrefix();

		if ($pathPrefix === null)
		{
			return $path;
		}

		return substr($path, strlen($pathPrefix));
	}

	/**
	 * Return a unique hash identifying this file
	 *
	 * @return  mixed
	 *
	 * @since   3.6
	 */
	public function getHash()
	{
		if (empty($this->filePath) || !file_exists($this->filePath))
		{
			return null;
		}

		return md5_file($this->filePath);
	}

	/**
	 * Detect the MIME type of a specific file
	 *
	 * @param   string $filePath
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getMimeType($filePath = null)
	{
		if (!empty($filePath))
		{
			$this->filePath = $filePath;
		}

		if (empty($this->filePath) || !file_exists($this->filePath))
		{
			return null;
		}

		$fileInfo = finfo_open(FILEINFO_MIME_TYPE);

		return finfo_file($fileInfo, $this->filePath);
	}
}
