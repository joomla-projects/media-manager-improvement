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

// @todo: Refactor this class

/**
 * Folder Media Controller
 *
 * @since  1.5
 */
class MediaControllerFolder extends JControllerLegacy
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

		$ret = true;

		foreach ($paths as $path)
		{
			if ($path !== JFile::makeSafe($path))
			{
				$dirName = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				$this->setWarning(JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_WARNDIRNAME', substr($dirName, strlen(COM_MEDIA_BASE))));

				continue;
			}

			$fullPath    = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			$fileObject = new JObject(array('filepath' => $fullPath));

			if (is_file($fileObject->filepath))
			{
				if (!$deleted = $this->deleteFile($fileObject))
				{
					$ret &= $deleted;
				}

				continue;
			}

			if (is_dir($fileObject->filepath))
			{
				if ($deleted = $this->deleteFolder($fileObject))
				{
					$ret &= $deleted;
				}
			}
		}

		return $ret;
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

		$folder      = $this->input->get('new-folder-name', '');
		$folderCheck = (string) $this->input->get('new-folder-name', null, 'raw');
		$parent      = $this->input->get('new-folder-base', '', 'path');

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

		if (($folderCheck !== null) && ($folder !== $folderCheck))
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'), 'warning');

			return false;
		}

		$path = JPath::clean(COM_MEDIA_BASE . '/' . $parent . '/' . $folder);

		if (is_dir($path) || is_file($path))
		{
			$this->input->set('folder', ($parent) ? $parent . '/' . $folder : $folder);
		}

		// Trigger the onContentBeforeSave event.
		$fileObject = new JObject(array('filepath' => $path));
		$result      = $this->triggerEvent('onContentBeforeSave', array('com_media.folder', &$fileObject, true));

		if (in_array(false, $result, true))
		{
			// There are some errors in the plugins
			$this->setWarning(JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $fileObject->getErrors()), implode('<br />', $errors)));

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
		if (!JFolder::create($folder->filepath))
		{
			return false;
		}

		$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
		JFile::write($folder->filepath . "/index.html", $data);

		// Trigger the onContentAfterSave event.
		$this->triggerEvent('onContentAfterSave', array('com_media.folder', &$folder, true));
		$this->setMessage(JText::sprintf('COM_MEDIA_CREATE_COMPLETE', substr($folder->filepath, strlen(COM_MEDIA_BASE))));

		return true;
	}

	/**
	 * Triggers the specified event
	 *
	 * @param string $eventName
	 * @param array  $eventArguments
	 */
	private function triggerEvent($eventName, $eventArguments)
	{
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger($eventName, $eventArguments);
	}

	/**
	 * Deletes file
	 *
	 * @return  boolean
	 */
	private function deleteFile(&$objectFile)
	{
		// Trigger the onContentBeforeDelete event.
		$result = $this->triggerEvent('onContentBeforeDelete', array('com_media.file', &$objectFile));

		if (in_array(false, $result, true))
		{
			// There are some errors in the plugins
			$errors = $objectFile->getErrors();
			$this->setWarning(JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));

			return false;
		}

		$ret = JFile::delete($objectFile->filepath);

		// Trigger the onContentAfterDelete event.
		$this->triggerEvent('onContentAfterDelete', array('com_media.file', &$objectFile));
		$this->setMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($objectFile->filepath, strlen(COM_MEDIA_BASE))));

		return $ret;
	}

	/**
	 * Deletes folder
	 *
	 * @return  boolean
	 */
	private function deleteFolder(&$fileObject)
	{
		$skipList = array(
			'.svn',
			'CVS',
			'.DS_Store',
			'__MACOSX',
			'index.html',
			'desktop.ini',
		);

		$contents = JFolder::files($fileObject->filepath, '.', true, false, $skipList);

		if (!empty($contents))
		{
			// This makes no sense...
			$folderPath = substr($fileObject->filepath, strlen(COM_MEDIA_BASE));
			$this->setWarning(JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', $folderPath));

			return false;
		}

		// Trigger the onContentBeforeDelete event.
		$result = $this->triggerEvent('onContentBeforeDelete', array('com_media.folder', &$fileObject));

		if (in_array(false, $result, true))
		{
			// There are some errors in the plugins
			$errors = $fileObject->getErrors();
			$this->setWarning(JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));

			return false;
		}

		$ret = JFolder::delete($fileObject->filepath);

		// Trigger the onContentAfterDelete event.
		$this->triggerEvent('onContentAfterDelete', array('com_media.folder', &$fileObject));
		$this->setMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($fileObject->filepath, strlen(COM_MEDIA_BASE))));

		return $ret;
	}

	/**
	 * Generate a warning
	 *
	 * @param $warning
	 */
	protected function setWarning($warning)
	{
		JError::raiseWarning(100, $warning);
	}
}
