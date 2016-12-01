<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * The file controller
 *
 * @since  1.6
 */
class MediaControllerFile extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// An file edit form can come from the files or featured view.
		// Adjust the redirect view on the value of 'return' in the request.
		if ($this->input->get('return') == 'featured')
		{
			$this->view_list = 'featured';
			$this->view_item = 'file&return=featured';
		}
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowAdd($data = array())
	{
		$categoryId = ArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
		$allow = null;

		if ($categoryId)
		{
			// If the category has been passed in the data or URL check it.
			$allow = JFactory::getUser()->authorise('core.create', 'com_media.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}

		return $allow;
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();

		// Zero record (id:0), return component edit permission by calling parent controller method
		if (!$recordId)
		{
			return parent::allowEdit($data, $key);
		}

		// Check edit on the record asset (explicit or inherited)
		if ($user->authorise('core.edit', 'com_media.file.' . $recordId))
		{
			return true;
		}

		// Check edit own on the record asset (explicit or inherited)
		if ($user->authorise('core.edit.own', 'com_media.file.' . $recordId))
		{
			// Existing record already has an owner, get it
			$record = $this->getModel()->getItem($recordId);

			if (empty($record))
			{
				return false;
			}

			// Grant if current user is owner of the record
			return $user->id == $record->created_by;
		}

		return false;
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		/** @var ContentModelFile $model */
		$model = $this->getModel('File', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_media&view=files' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

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
		$params = JComponentHelper::getParams('com_media');

		// Get some data from the request
		$files    = $this->input->files->get('Filedata', '', 'array');
		$return   = JFactory::getSession()->get('com_media.return_url');
		$category = JCategories::getInstance('Media')->get($this->input->getInt('category'));

		// Don't redirect to an external URL.
		if (!JUri::isInternal($return))
		{
			$return = '';
		}

		// Set the redirect
		if ($return)
		{
			$this->setRedirect($return . '&filter[category_id]=' . $category->id);
		}
		else
		{
			$this->setRedirect('index.php?option=com_media&filter[category_id]=' . $category->id);
		}

		if ($category == null || $category->id == 'root')
		{
			$this->setMessage(JText::_('COM_MEDIA_ERROR_UPLOAD_NO_CATEGORY'), 'error');
			return false;
		}

		// Authorize the user
		if (!$this->authoriseUser('create'))
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
		if (($postMaxSize > 0 && $contentLength > $postMaxSize)
				|| ($memoryLimit != -1 && $contentLength > $memoryLimit))
		{
			JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNUPLOADTOOLARGE'));

			return false;
		}

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			$file['name']     = JFile::makeSafe($file['name']);
			$file['name']     = str_replace(' ', '-', $file['name']);
			$file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $category->path, $file['name'])));

			if (($file['error'] == 1)
					|| ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
					|| ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
			{
				// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE'));

				return false;
			}

			if (JFile::exists($file['filepath']))
			{
				// A file with this name already exists
				JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));

				return false;
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by JFile::makeSafe)
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
			$this->getModel('File', 'MediaModel')->upload($file, $category);
			$this->setMessage(JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE))));
		}

		return true;
	}

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string  $action  - the action to be peformed (create or delete)
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_media'))
		{
			// User is not authorised
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
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));

			return false;
		}

		// Need this to enqueue messages.
		$app = JFactory::getApplication();

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();

		$ret = true;

		$safePaths = array_intersect($paths, array_map(array('JFile', 'makeSafe'), $paths));
		$unsafePaths = array_diff($paths, $safePaths);

		foreach ($unsafePaths as $path)
		{
			$path = JPath::clean(implode(DIRECTORY_SEPARATOR, array($folder, $path)));
			$path = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
			$app->enqueueMessage(JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', $path), 'error');
		}

		foreach ($safePaths as $path)
		{
			$fullPath = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			$object_file = new JObject(array('filepath' => $fullPath));

			if (is_file($object_file->filepath))
			{
				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', array('com_media.file', &$object_file));

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					$errors = $object_file->getErrors();
					JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));

					continue;
				}

				$ret &= JFile::delete($object_file->filepath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', array('com_media.file', &$object_file));
				$app->enqueueMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
			}
			elseif (is_dir($object_file->filepath))
			{
				$contents = JFolder::files($object_file->filepath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));

				if (!empty($contents))
				{
					// This makes no sense...
					$folderPath = substr($object_file->filepath, strlen(COM_MEDIA_BASE));
					JError::raiseWarning(100, JText::sprintf('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', $folderPath));

					continue;
				}

				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger('onContentBeforeDelete', array('com_media.folder', &$object_file));

				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					$errors = $object_file->getErrors();
					JError::raiseWarning(100, JText::plural('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors), implode('<br />', $errors)));

					continue;
				}

				$ret &= !JFolder::delete($object_file->filepath);

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onContentAfterDelete', array('com_media.folder', &$object_file));
				$app->enqueueMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
			}
		}

		return $ret;
	}
}
