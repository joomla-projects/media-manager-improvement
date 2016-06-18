<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/editor.php';

/**
 * HTML View class for a single file in the Media Manager
 *
 */
class MediaViewFile extends JViewLegacy
{
	/**
	 * @var    JConfig
	 * @since  3.6
	 */
	protected $config;

	/**
	 * @var JUser
	 */
	protected $user;

	/**
	 * @var JToolbar
	 */
	protected $bar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   3.6
	 */
	public function display($tpl = null)
	{
		$app          = JFactory::getApplication();
		$this->config = JComponentHelper::getParams('com_media');
		$this->user   = JFactory::getUser();
		$this->bar    = JToolbar::getInstance('toolbar');

		if (!$app->isAdmin())
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');

			return;
		}

		// Check for the right file
		$filePath     = $app->input->getPath('file');
		$fullFilePath = JPATH_ROOT . '/images/' . $filePath;

		if (is_file($fullFilePath) === false)
		{
			throw new RuntimeException(JText::_('JERROR_FILENOTFOUND' . ': ' . $filePath));
		}

		// Load the file object
		$fileModel = $this->getModel('file');
		$fileModel->loadByPath($fullFilePath);

		// @todo: Do not throw new file that does not exist yet in database, instead generate db entry
		if ($fileModel->getId() == 0)
		{
			throw new RuntimeException(JText::_('COM_MEDIA_ERROR_NO_FILE_IN_DB'));
		}

		$this->fileProperties = $fileModel->getFileProperties();
		$this->fileType       = $this->fileProperties['file_type'];

		// Set the toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add things to the toolbar
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function addToolbar()
	{
		$this->addToolbarPluginButtons();
		$this->addToolbarDelete();

		JToolbarHelper::title(JText::_('COM_MEDIA'), 'images mediamanager');
		JToolbarHelper::cancel('editor.cancel', 'JTOOLBAR_CLOSE');

		// Allow Media Editor plugins to modify the entire toolbar
		$toolbar    = JToolbar::getInstance('toolbar');
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onMediaEditorBeforeRenderToolbar', array(&$toolbar));
	}

	/**
	 * Add a delete button
	 */
	protected function addToolbarDelete()
	{
		// Add a delete button
		if (!$this->user->authorise('core.delete', 'com_media'))
		{
			return;
		}

		JToolbarHelper::custom('file.delete', 'delete', 'delete', 'JACTION_DELETE', false);
		JToolbarHelper::divider();
	}

	/**
	 * Add buttons per Media Editor plugin to the toolbar
	 */
	protected function addToolbarPluginButtons()
	{
		$toolbar = JToolbar::getInstance('toolbar');
		$plugins = JPluginHelper::getPlugin('media-editor');

		foreach ($plugins as $pluginData)
		{
			$pluginName = $pluginData->name;
			$plugin     = MediaHelperEditor::loadPlugin($pluginName);

			if (method_exists($plugin, 'onMediaEditorAllowed'))
			{
				if ($plugin->onMediaEditorAllowed($this->fileType) == false)
				{
					continue;
				}
			}

			$button = (object) array(
				'label'  => JText::_('PLG_MEDIA-EDITOR_' . strtoupper($pluginName) . '_BUTTON_LABEL'),
				'icon'   => 'plus',
				'width'  => 550,
				'height' => 400,
				'url'    => JUri::base() . 'index.php?option=com_media&view=editor&tmpl=component' . '&plugin=' . $pluginName . '&file=' . $this->fileProperties['path_relative'],
			);

			if (method_exists($plugin, 'onMediaEditorButtonLabel'))
			{
				$button->label = $plugin->onMediaEditorButtonLabel();
			}

			if (method_exists($plugin, 'onMediaEditorButtonIcon'))
			{
				$button->icon = $plugin->onMediaEditorButtonIcon();
			}

			if (method_exists($plugin, 'onMediaEditorButtonWidth'))
			{
				$button->width = $plugin->onMediaEditorButtonWidth();
			}

			if (method_exists($plugin, 'onMediaEditorButtonHeight'))
			{
				$button->height = $plugin->onMediaEditorButtonHeight();
			}

			$toolbar->appendButton('Popup', $button->icon, $button->label, $button->url, $button->width, $button->height);
		}
	}
}
