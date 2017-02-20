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
		$doc->addScript(JUri::root() . 'media/com_media/js/mediamanager.js');

		// Add stylesheets
        $doc->addStyleSheet(JUri::root() . 'media/com_media/css/mediamanager.css');

        // TODO include the font in the component media (self hosted)
        $doc->addStyleSheet('https://fonts.googleapis.com/icon?family=Material+Icons');

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
		// Set the title
		JToolbarHelper::title(JText::_('COM_MEDIA'), 'images mediamanager');
	}
}
