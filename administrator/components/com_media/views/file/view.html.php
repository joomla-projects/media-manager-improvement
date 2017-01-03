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

/**
 * View to edit an file.
 *
 * @todo Prototype!
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaViewFile extends JViewLegacy
{

	/**
	 * The plugins available for this media type
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $plugins = array();

	/**
	 * The plugin categories available for this media type
	 *
	 * @var    array
	 * @since  __DEPLOY_VERSION__
	 */
	protected $pluginCategories = array();

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		$this->file = $input->getString('file', null);

		if (!$this->file && JFile::exists(JPATH_ROOT . '/' . $this->file))
		{
			// @todo error handling controller redirect files
			throw new Exception('Image file does not exist');
		}

		// Load media action plugins
		$this->loadPlugins(pathinfo($this->file, PATHINFO_EXTENSION));

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the toolbar buttons
	 *
	 * @return  void
	 *
	 * @since   _DEPLOY_VERSION
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_MEDIA_EDIT'), 'images mediamanager');

		// @todo buttons
		JToolbarHelper::apply('file.apply');
		JToolbarHelper::save('file.save');
	}


	/**
	 * Load the available action plugins
	 *
	 * @param   string  $fileExtension  File Extension
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function loadPlugins($fileExtension)
	{
		$allPlugins    = JPluginHelper::getPlugin('media-action');

		foreach ($allPlugins as $option)
		{
			// Load Plugin @todo improve
			include_once JPATH_ROOT . '/plugins/media-action/' . $option->name . '/' . $option->name . '.php';
			$className = 'PlgMediaAction' . ucfirst($option->name);

			try
			{
				$supportedExtensions = call_user_func($className . '::getMediaFileExtensions');
			}
			catch (Exception $e)
			{
				// Ignore
				continue;
			}

			if (in_array($fileExtension, $supportedExtensions))
			{
				/** @var MediaAction $plugin */
				$plugin = new $className($option->name);

				$this->pluginCategories[] = $plugin->getCategory();
				$this->plugins[]          = $plugin;
			}
		}

		// @todo move
		$this->pluginCategories = array_unique($this->pluginCategories);
		sort($this->pluginCategories);
	}
}
