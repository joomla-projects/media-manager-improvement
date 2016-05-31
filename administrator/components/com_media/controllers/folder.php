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
jimport('joomla.filesystem.folder');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/controller.php';

/**
 * Folder Media Controller
 *
 * @since  1.5
 */
class MediaControllerFolder extends MediaController
{
	/**
	 * Deletes paths from the current path
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function delete()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();

		// Get some data from the request
		$tmpl   = $this->input->get('tmpl');
		$paths  = $this->input->get('rm', array(), 'array');
		$folder = $this->input->get('folder', '', 'path');

		if (empty($folder))
		{
			$folder = $this->getFoldersModel()
				->getCurrentFolder();
		}

		$redirect = 'index.php?option=com_media&folder=' . $folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}

		$this->setRedirect($redirect);

		// Just return if there's nothing to do
		if (empty($paths))
		{
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');

			return true;
		}

		if (!$user->authorise('core.delete', 'com_media'))
		{
			// User is not authorised to delete
			$this->setWarning(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		foreach ($paths as $path)
		{
			$this->deletePath($path);
		}

		return true;
	}

	/**
	 * Create a folder
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function create()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user = JFactory::getUser();

		$folder      = $this->input->get('new-folder-name', '', 'path');
		$parent      = $this->input->get('new-folder-base', '', 'path');

		if (empty($parent))
		{
			$parent = $this->getFoldersModel()
				->getCurrentFolder();
		}

		$this->setRedirect('index.php?option=com_media&folder=' . $parent . '&tmpl=' . $this->input->get('tmpl', 'index'));

		// File name is of zero length (null)
		if (!strlen($folder))
		{
			$this->setWarning(JText::_('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_EMPTY'));

			return false;
		}

		// User is not authorised to create
		if (!$user->authorise('core.create', 'com_media'))
		{
			$this->setWarning(JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED'));

			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		$this->input->set('folder', $parent);

		$path = JPath::clean(COM_MEDIA_BASE . '/' . $parent . '/' . $folder);

		if (is_dir($path) || is_file($path))
		{
			$this->input->set('folder', ($parent) ? $parent . '/' . $folder : $folder);
		}

		// Trigger the onContentBeforeSave event.
		$fileObject = new JObject(array('filepath' => $path));
		$result     = $this->triggerEvent('onContentBeforeSave', array('com_media.folder', &$fileObject, true));

		if (in_array(false, $result, true))
		{
			// There are some errors in the plugins
			$this->setWarning(JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $fileObject->getErrors()), implode('<br />', $errors)));

			return false;
		}

		// Try to create the folder
		try
		{
			$this->getFolderModel()->create($parent . '/' . $folder);
		}
		catch(Exception $e)
		{
			// There are some errors in the plugins
			$this->setWarning('EXCEPTION: ' . $e->getMessage());

			return false;
		}

		$this->createIndexFileInFolder($fileObject);

		$this->input->set('folder', ($parent) ? $parent . '/' . $folder : $folder);

		return true;
	}

	/**
	 * Create an index.html file in the folder
	 *
	 * @param object $folder
	 *
	 * @return bool
	 */
	private function createIndexFileInFolder($folder)
	{
		$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
		JFile::write($folder->filepath . "/index.html", $data);

		// Trigger the onContentAfterSave event.
		$this->triggerEvent('onContentAfterSave', array('com_media.folder', &$folder, true));
		$this->setMessage(JText::sprintf('COM_MEDIA_CREATE_COMPLETE', substr($folder->filepath, strlen(COM_MEDIA_BASE))));

		return true;
	}
}
