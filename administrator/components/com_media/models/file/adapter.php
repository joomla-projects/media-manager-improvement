<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/adapter/interface/adapter.php';
require_once __DIR__ . '/adapter/interface/flysystem.php';
require_once __DIR__ . '/adapter/abstract.php';

/**
 * Media Component File Adapter Model
 */
class MediaModelFileAdapter
{
	/**
	 * List of available file adapter objects (possibly extended by plugins)
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $availableFileAdapters = array();

	/**
	 * List of available file adapter identifiers (made available by this component)
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $defaultFileAdapterIdentifiers = array('local');

	/**
	 * Default file adapter
	 *
	 * @var    MediaModelFileAdapterInterfaceAdapter
	 * @since  3.6
	 */
	protected $defaultFileAdapter = 'local';

	/**
	 * Return the current file adapter object
	 *
	 * @param  string $adapterName
	 *
	 * @return  mixed|false
	 *
	 * @since   3.6
	 */
	public function getFileAdapter($adapterName)
	{
		foreach ($this->getAvailableFileAdapters() as $availableFileAdapterName => $availableFileAdapter)
		{
			if ($availableFileAdapterName == $adapterName)
			{
				return $availableFileAdapter;
			}
		}

		return false;
	}

	/**
	 * Method to get the support file adapters
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	protected function getAvailableFileAdapters()
	{
		if (empty($this->availableFileAdapters))
		{
			foreach ($this->defaultFileAdapterIdentifiers as $defaultFileAdapterIdentifier)
			{
				$fileAdapter = $this->getFileAdapterObjectFromIdentifier($defaultFileAdapterIdentifier);

				if ($fileAdapter == false)
				{
					continue;
				}

				$this->availableFileAdapters[$defaultFileAdapterIdentifier] = $fileAdapter;
			}

			// Allow plugins to modify this listing of adapter types
			$this->modifyAvailableFileAdapters();
		}

		return $this->availableFileAdapters;
	}

	/**
	 * Modify the list of available file adapters through the plugin event onMediaBuildFileAdapters()
	 *
	 * @return  void
	 *
	 * @since  3.6
	 */
	protected function modifyAvailableFileAdapters()
	{
		JPluginHelper::importPlugin('media');

		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onMediaBuildFileAdapters', array(&$this->availableFileAdapters));
	}

	/**
	 * Get a file adapter object based on an identifier string
	 *
	 * @param   string $identifier
	 *
	 * @return  bool|MediaModelFileAdapterInterfaceAdapter
	 *
	 * @since   3.6
	 */
	protected function getFileAdapterObjectFromIdentifier($identifier)
	{
		if (empty($identifier))
		{
			return false;
		}

		$identifierFile = __DIR__ . '/adapter/' . $identifier . '.php';

		if (!is_file($identifierFile))
		{
			return false;
		}

		include_once $identifierFile;

		$fileAdapterClass = 'MediaModelFileAdapter' . ucfirst($identifier);
		$fileType         = new $fileAdapterClass(JPATH_ROOT);

		return $fileType;
	}
}
