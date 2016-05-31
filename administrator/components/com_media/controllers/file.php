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

/**
 * Media File Controller
 *
 * @since  1.5
 */
class MediaControllerFile extends JControllerLegacy
{
	/**
	 * The folder we are uploading into
	 *
	 * @var    string
	 *
	 * @since  1.5
	 */
	protected $folder = '';

	/**
	 * Upload one or more files
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function upload()
	{
		// Check for request forgeries
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get some data from the request
		$files  = $this->input->files->get('Filedata', '', 'array');
		$return = JFactory::getSession()->get('com_media.return_url');

		$this->folder = $this->input->get('folder', '', 'path');

		// Don't redirect to an external URL.
		if (!JUri::isInternal($return))
		{
			$return = '';
		}

		// Set the redirect
		$return = $return ?: 'index.php?option=com_media';
		$this->setRedirect($return . '&folder=' . $this->folder);

		// Authorize the user
		if (!$this->isUserAuthorized('create'))
		{
			return false;
		}

		// Total length of post back data in bytes.
		$contentLength = (int) $_SERVER['CONTENT_LENGTH'];

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		// Maximum allowed size of post back data in MB.
		$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

		// Maximum allowed size of script execution in MB.
		$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

		// Check for the total size of post back data.
		if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit != -1 && $contentLength > $memoryLimit))
		{
			$this->setWarning(JText::_('COM_MEDIA_ERROR_WARNUPLOADTOOLARGE'));

			return false;
		}

		// Get com_config params
		$params = JComponentHelper::getParams('com_media');

		$uploadMaxSize     = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			$file['name']     = JFile::makeSafe($file['name']);
			$file['name']     = str_replace(' ', '-', $file['name']);
			$file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $file['name'])));

			// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
			if (($file['error'] == 1) || ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize) || ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
			{
				$this->setWarning(JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE'));

				return false;
			}

			// A file with this name already exists
			if (JFile::exists($file['filepath']))
			{
				$this->setWarning(JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));

				return false;
			}

			// No filename (after the name was cleaned by JFile::makeSafe)
			if (!isset($file['name']))
			{
				$this->setRedirect('index.php', JText::_('COM_MEDIA_INVALID_REQUEST'), 'error');

				return false;
			}
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();

		foreach ($files as &$file)
		{
			// The request is valid
			$err = null;

			// The file can't be uploaded
			if (!MediaHelper::canUpload($file, $err))
			{
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$fileObject = new JObject($file);

			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', &$fileObject, true));

			// There are some errors in the plugins
			if (in_array(false, $result, true))
			{
				$this->setWarning(JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $fileObject->getErrors()), implode('<br />', $errors)));

				return false;
			}

			// Error in upload
			if (!JFile::upload($fileObject->tmp_name, $fileObject->filepath))
			{
				$this->setWarning(JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));

				return false;
			}

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$fileObject, true));

			$this->setMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($fileObject->filepath, strlen(COM_MEDIA_BASE))));
		}

		return true;
	}

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string $action - the action to be performed (create or delete)
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function isUserAuthorized($action)
	{
		$user = JFactory::getUser();

		// User is not authorised
		if (!$user->authorise('core.' . strtolower($action), 'com_media'))
		{
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));

			return false;
		}

		return true;
	}

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

		// Get some data from the request
		$tmpl   = $this->input->get('tmpl');
		$paths  = $this->input->get('rm', array(), 'array');
		$folder = $this->input->get('folder', '', 'path');

		$redirect = 'index.php?option=com_media&folder=' . $folder;

		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component'; // @todo: Refactor this
		}

		$this->setRedirect($redirect);

		// Nothing to delete
		if (empty($paths))
		{
			$this->setWarning(JText::_('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILES_EMPTY'));
			
			return true;
		}

		// Authorize the user
		if (!$this->isUserAuthorized('delete'))
		{
			$this->setWarning(JText::_('COM_MEDIA_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		$ret = true;

		foreach ($paths as $path)
		{
			if ($path !== JFile::makeSafe($path))
			{
				// Filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				$this->setWarning(JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_MEDIA_BASE))));

				continue;
			}

			$fullPath    = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			$fileObject = new JObject(array('filepath' => $fullPath));

			if (is_file($fileObject->filepath))
			{
				if(!$deleted = $this->deleteFile($fileObject))
				{
					$ret &= $deleted;
				}

				continue;
			}

			if (is_dir($fileObject->filepath))
			{
				if($deleted = $this->deleteFolder($fileObject))
				{
					$ret &= $deleted;
				}
			}
		}

		return $ret;
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
	private function deleteFile(&$fileObject)
	{
		// Trigger the onContentBeforeDelete event.
		$result = $this->triggerEvent('onContentBeforeDelete', array('com_media.file', &$fileObject));

		if (in_array(false, $result, true))
		{
			// There are some errors in the plugins
			$errors = $fileObject->getErrors();
			$this->setWarning(JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));

			return false;
		}

		$ret = JFile::delete($fileObject->filepath);

		// Trigger the onContentAfterDelete event.
		$this->triggerEvent('onContentAfterDelete', array('com_media.file', &$fileObject));
		$this->setMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($fileObject->filepath, strlen(COM_MEDIA_BASE))));

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
		$this->setWarning($warning);
	}
}
