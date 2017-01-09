<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Media List View
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaViewMedia extends JViewLegacy
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
		// Prepare the document
		$this->prepareDocument();

		// Prepare the toolbar
		$this->prepareToolbar();

		parent::display($tpl);
	}

	/**
	 * Prepare the document.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function prepareDocument()
	{
		$doc = JFactory::getDocument();

		// Add javascripts
		$doc->addScript(JUri::root() . 'media/media/js/app.js');

		// Populate the language
		// TODO use JText for all language strings used by the js application
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
		$bar  = JToolbar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the title
		JToolbarHelper::title(JText::_('COM_MEDIA'), 'images mediamanager');

		// TODO add the toolbar buttons

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.uploadmedia', JPATH_COMPONENT_ADMINISTRATOR . '/legacy/layouts');

		$bar->appendButton('Custom', $layout->render(array()), 'upload');
		JToolbarHelper::divider();

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.newfolder', JPATH_COMPONENT_ADMINISTRATOR . '/legacy/layouts');

		$bar->appendButton('Custom', $layout->render(array()), 'upload');
		JToolbarHelper::divider();

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.deletemedia', JPATH_COMPONENT_ADMINISTRATOR . '/legacy/layouts');

		$bar->appendButton('Custom', $layout->render(array()), 'upload');
		JToolbarHelper::divider();

		JToolbarHelper::preferences('com_media');
		JToolbarHelper::divider();

		JToolbarHelper::help('JHELP_CONTENT_MEDIA_MANAGER');
	}
}
