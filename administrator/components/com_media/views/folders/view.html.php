<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Media component
 *
 * @since  1.0
 */
class MediaViewFolders extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$config = JComponentHelper::getParams('com_media');

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$images  = $this->get('images');
		$folders = $this->get('folders');
		$state   = $this->get('state');

		$this->session     = JFactory::getSession();
		$this->config      = $config;
		$this->state       = $this->get('state');
		$this->folders     = $this->get('foldersTree');
		$this->require_ftp = $ftp;
		$this->baseURL     = COM_MEDIA_BASEURL;
		$this->images      = &$images;
		$this->folders     = &$folders;
		$this->state       = &$state;

		$user  = JFactory::getUser();

		if ($this->state->folder === "")
		{
			$this->state->folder = COM_MEDIA_BASEURL;
		}

		JToolbarHelper::title(JText::_('COM_MEDIA_FILES'), 'media manager');

		if ($user->authorise('core.admin', 'com_media') || $user->authorise('core.options', 'com_media'))
		{
			JToolbarHelper::preferences('com_media');
		}

		JToolbarHelper::help('JHELP_COMPONENTS_MEDIA_FILES');

		parent::display($tpl);
	}

	/**
	 * Display a folder level
	 *
	 * @param   array $folder Array with folder data
	 *
	 * @return  string
	 *
	 * @since   1.0
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
