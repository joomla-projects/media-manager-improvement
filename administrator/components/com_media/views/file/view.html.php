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
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   3.6
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->config = JComponentHelper::getParams('com_media');

		if (!$app->isAdmin())
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');

			return;
		}

		// Check for the right file
		$filePath = $app->input->getPath('file');
		$fullPath = JPATH_ROOT . '/images/' . $filePath;

		if (is_file($fullPath) === false)
		{
			throw new RuntimeException(JText::_('JERROR_FILENOTFOUND' . ': ' . $filePath));
		}

		// Load the file object
		$fileModel = $this->getModel('file');
		$fileModel->loadByPath($fullPath);

		if ($fileModel->getId() == 0)
		{
			throw new RuntimeException(JText::_('COM_MEDIA_ERROR_NO_FILE_IN_DB'));
		}

		$this->fileProperties = $fileModel->getFileProperties();
		$this->fileType = $this->fileProperties['file_type'];

		// Set the toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function addToolbar()
	{
		$plugins = JPluginHelper::getPlugin('media-editor');
		$toolbar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_MEDIA'), 'images mediamanager');
		JToolbarHelper::cancel('editor.cancel', 'JTOOLBAR_CLOSE');

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

			if (method_exists($plugin, 'onMediaEditorButtonLabel'))
			{
				$buttonLabel = $plugin->onMediaEditorButtonLabel();
			}
			else
			{
				$buttonLabel = JText::_('PLG_MEDIA-EDITOR_' . strtoupper($pluginName) . '_BUTTON_LABEL');
			}

			if (method_exists($plugin, 'onMediaEditorButtonIcon'))
			{
				$buttonIcon = $plugin->onMediaEditorButtonIcon();
			}
			else
			{
				$buttonIcon = 'plus';
			}

			if (method_exists($plugin, 'onMediaEditorButtonWidth'))
			{
				$buttonWidth = $plugin->onMediaEditorButtonWidth();
			}
			else
			{
				$buttonWidth = 550;
			}

			if (method_exists($plugin, 'onMediaEditorButtonHeight'))
			{
				$buttonHeight = $plugin->onMediaEditorButtonHeight();
			}
			else
			{
				$buttonHeight = 400;
			}

			$buttonUrl = JUri::base() . 'index.php?option=com_media&view=editor&tmpl=component&plugin=' . $pluginName . '&file=' . $this->fileProperties['path_relative'];

			$toolbar->appendButton('Popup', $buttonIcon, $buttonLabel, $buttonUrl, $buttonWidth, $buttonHeight);
		}
	}
}