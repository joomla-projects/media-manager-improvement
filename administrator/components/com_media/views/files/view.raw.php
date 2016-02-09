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
class MediaViewFiles extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		// Do not allow cache
		JFactory::getApplication()->allowCache(false);

		$foldersModel = JModelLegacy::getInstance('folders', 'MediaModel');

		$mediaFiles  = $this->get('files');
		$folders = $foldersModel->getFolders(COM_MEDIA_BASE);
		$state   = $this->get('state');

		$this->baseURL = COM_MEDIA_BASEURL;
		$this->mediaFiles  = &$mediaFiles;
		$this->folders = &$folders;
		$this->state   = &$state;

		parent::display($tpl);
	}

	/**
	 * Set the active folder
	 *
	 * @param   integer  $index  Folder position
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setActFolder($index = 0)
	{
		if (isset($this->folders[0][$index]))
		{
			$this->_tmp_folder = &$this->folders[0][$index];
		}
		else
		{
			$this->_tmp_folder = new JObject;
		}
	}

	/**
	 * Set the active image
	 *
	 * @param   integer  $index  Image position
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setImage($index = 0)
	{
		if (isset($this->mediaFiles[$index]))
		{
			$this->_tmp_img = &$this->mediaFiles[$index];
		}
		else
		{
			$this->_tmp_img = new JObject;
		}
	}
}
