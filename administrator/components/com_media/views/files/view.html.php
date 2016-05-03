<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once 'common.php';

/**
 * HTML View class for the Media component
 *
 * @since  3.6
 */
class MediaViewFiles extends MediaViewFilesCommon
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
		// Do not allow cache
		$app = JFactory::getApplication();
		$app->allowCache(false);

		$foldersModel = JModelLegacy::getInstance('folders', 'MediaModel');

		$files   = $this->get('files');
		$folders = $foldersModel->getFolders(COM_MEDIA_BASE);
		$state   = $this->get('state');

		$this->baseURL = COM_MEDIA_BASEURL;
		$this->files   = $files;
		$this->folders = $folders;
		$this->state   = $state;

		parent::display($tpl);
	}
}
