<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\View\HtmlView;

/**
 * Media List View
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaViewMedia extends HtmlView
{
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
		// Prepare the toolbar
		$this->prepareToolbar();

		// Get enabled adapters
		$this->providers = $this->get('Providers');

		parent::display($tpl);
	}

	/**
	 * Prepare the toolbar.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function prepareToolbar()
	{
		// Get the toolbar object instance
		$bar  = JToolbar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the title
		JToolbarHelper::title(JText::_('COM_MEDIA'), 'images mediamanager');

		// Add the upload and create folder buttons
		if ($user->authorise('core.create', 'com_media'))
		{
			// Add the upload button
			$layout = new JLayoutFile('toolbar.upload', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');

			$bar->appendButton('Custom', $layout->render(array()), 'upload');
			JToolbarHelper::divider();

			// Add the create folder button
			$layout = new JLayoutFile('toolbar.create-folder', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');

			$bar->appendButton('Custom', $layout->render(array()), 'new');
			JToolbarHelper::divider();
		}

		// Add a delete button
		if ($user->authorise('core.delete', 'com_media'))
		{
			// Instantiate a new JLayoutFile instance and render the layout
			$layout = new JLayoutFile('toolbar.delete');

			$bar->appendButton('Custom', $layout->render(array()), 'delete');
			JToolbarHelper::divider();
		}

		// Add the preferences button
		if ($user->authorise('core.admin', 'com_media') || $user->authorise('core.options', 'com_media'))
		{
			JToolbarHelper::preferences('com_media');
			JToolbarHelper::divider();
		}

		JToolbarHelper::help('JHELP_CONTENT_MEDIA_MANAGER');
	}
}
