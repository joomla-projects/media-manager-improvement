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
 * Media Manager Component Controller
 *
 * @since  1.5
 */
class MediaController extends JControllerLegacy
{
	/**
	 * MediaController constructor.
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;
		$viewName    = $this->input->get('view');

		if (empty($viewName))
		{
			$this->input->set('view', 'folders');
		}

		if ($viewName == 'images')
		{
			$this->input->set('filter', 'image');
			$this->input->set('view', 'folders');
		}

		$rt = parent::__construct($config);

		return $rt;
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController        This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// @todo: Is this needed here?
		JPluginHelper::importPlugin('content');

		return parent::display($cachable, $urlparams);
	}

	/**
	 * Validate FTP credentials
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function ftpValidate()
	{
		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
	}

	/**
	 * Delete a path - either a file or a folder
	 *
	 * @param $path
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function deletePath($path)
	{
		$fileObject = $this->getFileModel()
			->loadByPath($path);

		if ($fileObject instanceof MediaModelFile)
		{
			try
			{
				if ($fileObject->delete())
				{
					$this->setMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', basename($path)));

					return true;
				}
			}
			catch (Exception $e)
			{
				$this->setWarning($e->getMessage());
			}

			$this->setWarning(JText::_('COM_MEDIA_ERROR_UNABLE_TO_DELETE') . basename($path));

			return false;
		}

		$folderObject = $this->getFolderModel()
			->loadByPath($path);

		if ($folderObject instanceof MediaModelFolder)
		{
			try
			{
				if ($folderObject->delete())
				{
					$this->setMessage(JText::sprintf('COM_MEDIA_DELETE_COMPLETE', basename($path)));

					return true;
				}
			}
			catch (Exception $e)
			{
				$this->setWarning($e->getMessage());
			}

			$this->setWarning(JText::_('COM_MEDIA_ERROR_UNABLE_TO_DELETE') . basename($path));

			return false;
		}

		$this->setWarning(JText::_('COM_MEDIA_ERROR_BAD_REQUEST'));

		return false;
	}

	/**
	 * Triggers the specified event
	 *
	 * @param string $eventName
	 * @param array  $eventArguments
	 */
	protected function triggerEvent($eventName, $eventArguments)
	{
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger($eventName, $eventArguments);
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

	/**
	 * Return the folder model
	 *
	 * @return  MediaModelFolder
	 */
	protected function getFolderModel()
	{
		return JModelLegacy::getInstance('folder', 'MediaModel');
	}

	/**
	 * Return the file model
	 *
	 * @return  MediaModelFile
	 */
	protected function getFileModel()
	{
		return JModelLegacy::getInstance('file', 'MediaModel');
	}

	/**
	 * Return the folders model
	 *
	 * @return  MediaModelFolders
	 */
	protected function getFoldersModel()
	{
		return JModelLegacy::getInstance('folders', 'MediaModel');
	}
}
