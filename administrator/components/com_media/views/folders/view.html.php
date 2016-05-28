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
 * HTML View class for the Media component
 *
 * @since  3.6
 */
class MediaViewFolders extends JViewLegacy
{
	/**
	 * Current state object
	 *
	 * @var    mixed
	 * @since  3.6
	 */
	protected $state;

	/**
	 * List of subfolders
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $folders;

	/**
	 * @var    JSession
	 * @since  3.6
	 */
	protected $session;

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
		$this->user = JFactory::getUser();
		$this->bar  = JToolbar::getInstance('toolbar');

		$ftp = !JClientHelper::hasCredentials('ftp');

		$images        = $this->get('images');
		$subfolders    = $this->get('folders');
		$folders       = $this->getModel()->getFolders(COM_MEDIA_BASE);
		$currentFolder = $this->getModel()->getCurrentFolder();
		$state         = $this->get('state');

		$this->session        = JFactory::getSession();
		$this->config         = JComponentHelper::getParams('com_media');
		$this->state          = $this->get('state');
		$this->require_ftp    = $ftp;
		$this->images         = $images;
		$this->folders        = $folders;
		$this->current_folder = $currentFolder;
		$this->subfolders     = $subfolders;
		$this->state          = $state;

		if ($this->state->folder === "")
		{
			$this->state->folder = COM_MEDIA_BASEURL;
		}

		// Set the toolbar
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_MEDIA'), 'images mediamanager');

		$this->addToolbarUpload();
		$this->addToolbarCreateFolder();
		$this->addToolbarDelete();
		$this->addToolbarPreferences();

		JToolbarHelper::help('JHELP_CONTENT_MEDIA_MANAGER');
	}

	/**
	 * Add an upload button
	 */
	protected function addToolbarUpload()
	{
		if ($this->user->authorise('core.create', 'com_media'))
		{
			// Instantiate a new JLayoutFile instance and render the layout
			$layout = new JLayoutFile('toolbar.uploadmedia');

			$this->bar->appendButton('Custom', $layout->render(array()), 'upload');
			JToolbarHelper::divider();
		}
	}

	/**
	 * Add a create folder button
	 */
	protected function addToolbarCreateFolder()
	{
		if ($this->user->authorise('core.create', 'com_media'))
		{
			// Instantiate a new JLayoutFile instance and render the layout
			$layout = new JLayoutFile('toolbar.newfolder');

			$this->bar->appendButton('Custom', $layout->render(array()), 'upload');
			JToolbarHelper::divider();
		}
	}

	/**
	 * Add a delete button
	 */
	protected function addToolbarDelete()
	{
		// Add a delete button
		if ($this->user->authorise('core.delete', 'com_media'))
		{
			// Instantiate a new JLayoutFile instance and render the layout
			$layout = new JLayoutFile('toolbar.deletemedia');

			$this->bar->appendButton('Custom', $layout->render(array()), 'upload');
			JToolbarHelper::divider();
		}
	}

	/**
	 * Add a preferences button
	 */
	protected function addToolbarPreferences()
	{        // Add a preferences button
		if ($this->user->authorise('core.admin', 'com_media') || $this->user->authorise('core.options', 'com_media'))
		{
			JToolbarHelper::preferences('com_media');
			JToolbarHelper::divider();
		}

	}

	/**
	 * Display a folder level
	 *
	 * @param   array $folder Array with folder data
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	protected function getFolderLevel($folder)
	{
		$this->folders_id = null;
		$txt              = null;

		if (isset($folder['children']) && count($folder['children']))
		{
			$tmp           = $this->folders;
			$this->folders = $folder;
			$txt           = $this->loadTemplate('folders');
			$this->folders = $tmp;
		}

		return $txt;
	}
}
