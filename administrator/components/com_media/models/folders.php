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

require_once JPATH_ADMINISTRATOR . '/components/com_media/helpers/folder.php';

/**
 * Media Component Folders Model
 *
 * @since  3.6
 */
class MediaModelFolders extends JModelLegacy
{
	/**
	 * Lists the folders in a parent folder
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $folders = array();

	/**
	 * MediaModelFolders constructor.
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$rt = parent::__construct($config);

		$this->detectCurrentFolder();

		return $rt;
	}

	/**
	 * Get the current folder
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getCurrentFolder()
	{
		return $this->getState('current_folder');
	}

	/**
	 * Set the current folder
	 *
	 * @param   string $currentFolder
	 *
	 * @return  $this
	 *
	 * @since   3.6
	 */
	public function setCurrentFolder($currentFolder)
	{
		// Simple sanitizing
		$currentFolder = preg_replace('/^([\/]+)/', '', $currentFolder);

		// Save this folder in the session
		$session = JFactory::getSession();
		$session->set('com_media.current_folder', $currentFolder);

		// Set the state
		$this->setState('current_folder', $currentFolder);

		return $this;
	}

	/**
	 * Get the folder tree
	 *
	 * @param   mixed $base Base folder | null for using base media folder
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getFolders($currentBase = null)
	{
		$currentBase = MediaHelperFolder::sanitizePath($currentBase);

		// Get some paths from the request
		if (empty($currentBase))
		{
			$currentFolder = $this->getState('current_folder');
			$currentBase = COM_MEDIA_BASE;

			if (!empty($currentFolder))
			{
				$currentBase .= '/' . $currentFolder;
			}
		}

		$mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE . '/');

		// Get the list of folders
		jimport('joomla.filesystem.folder');
		$folders = JFolder::folders($currentBase, '.', true, true);

		$tree = array();

		foreach ($folders as $folder)
		{
			$folder   = str_replace(DIRECTORY_SEPARATOR, '/', $folder);
			$name     = substr($folder, strrpos($folder, '/') + 1);
			$relative = str_replace($mediaBase, '', $folder);
			$absolute = $folder;
			$path     = explode('/', $relative);
			$node     = (object) array('name' => $name, 'relative' => $relative, 'absolute' => $absolute);
			$tmp      = &$tree;

			for ($i = 0, $n = count($path); $i < $n; $i++)
			{
				if (!isset($tmp['children']))
				{
					$tmp['children'] = array();
				}

				if ($i == $n - 1)
				{
					// We need to place the node
					$tmp['children'][$relative] = array('data' => $node, 'children' => array());

					break;
				}

				if (array_key_exists($key = implode('/', array_slice($path, 0, $i + 1)), $tmp['children']))
				{
					$tmp = &$tmp['children'][$key];
				}
			}
		}

		if ($currentBase !== COM_MEDIA_BASE)
		{
			$tree['parent'] = $this->getParent();
		}

		$tree['data'] = (object) array('name' => JText::_('COM_MEDIA_MEDIA'), 'relative' => '', 'absolute' => $currentBase);

		return $tree;
	}

	/**
	 * Get the parent folder
	 */
	public function getParent()
	{
		$currentBase = COM_MEDIA_BASE . '/' . $this->getState('current_folder');
		$parent = dirname($currentBase);

		if ($parent)
		{
			$parent = MediaHelperFolder::sanitizePath($parent);
			$parent = MediaHelperFolder::fromAbsoluteToRelative($parent);
		}

		return $parent;
	}

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
			$input = JFactory::getApplication()->input;

			$folder = $input->get('folder', '', 'path');
			$this->setState('folder', $folder);

			$fieldid = $input->get('fieldid', '');
			$this->setState('field.id', $fieldid);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			$set = true;
		}

		return parent::getState($property, $default);
	}

	/**
	 * @throws Exception
	 */
	protected function detectCurrentFolder()
	{
		$app     = JFactory::getApplication();
		$getData = $app->input->get->getArray();

		if (isset($getData['folder']))
		{
			$currentFolder = $app->input->get('folder', '', 'path');
			$this->setCurrentFolder($currentFolder);

			return;
		}

		$session       = JFactory::getSession();
		$currentFolder = $session->get('com_media.current_folder');

		if (!empty($currentFolder))
		{
			$this->setCurrentFolder($currentFolder);
		}
	}

	/**
	 * Return the file model
	 *
	 * @return  MediaModelFolder
	 *
	 * @since   3.6
	 */
	protected function getFolderModel()
	{
		return JModelLegacy::getInstance('folder', 'MediaModel');
	}
}
