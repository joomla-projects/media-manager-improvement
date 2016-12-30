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
 * Abstract class for the Media Manage Effects (E.g. Crop, Rotate, Convert)
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class MediaAction implements MediaActionInterface
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var     boolean
	 * @since   __DEPLOY_VERSION_
	 */
	protected $autoloadLanguage = false;

	/**
	 * The Plugin Name
	 *
	 * @var     string
	 * @since   __DEPLOY_VERSION_
	 */
	protected $name;

	/**
	 * Constructor
	 *
	 * @param   string  $name  The plugin name
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($name)
	{
		$this->name = $name;

		// Load the language files if needed.
		if ($this->autoloadLanguage)
		{
			$this->loadLanguage($name);
		}
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string  $name      The plugin for which a language file should be loaded
	 * @param   string  $basePath  The basepath to use
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function loadLanguage($name, $basePath = JPATH_ADMINISTRATOR)
	{
		$name = strtolower('plg_media-action_' . $name);

		$lang = JFactory::getLanguage();

		// If language already loaded, don't load it again.
		if ($lang->getPaths($name))
		{
			return true;
		}

		return $lang->load($name, $basePath, null, false, true);
	}

	/**
	 * Get the plugin name
	 *
	 * @return  string  Name of the Plugin
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getName()
	{
		return $this->name;
	}
}
