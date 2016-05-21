<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  MediaEditor.Rename
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Class PlgMediaEditorExample
 */
class PlgMediaEditorImagecropper extends JPlugin
{
	/**
	 * Load the application automatically
	 */
	protected $app;

	/**
	 * Load the language file automatically
	 *
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * Method to check whether this media editor plugin is allowed on a specific fileType
	 *
	 * @param $fileType string
	 *
	 * @return bool
	 */
	public function onMediaEditorAllowed($fileType)
	{
		return $fileType == 'image';
	}

	/**
	 * Method to return the button label of this plugin
	 *
	 * @return string
	 */
	public function onMediaEditorButtonLabel()
	{
		return JText::_('PLG_MEDIA-EDITOR_IMAGECROPPER_BUTTON_LABEL');
	}

	/**
	 * Method to return the HTML shown in a modal popup within the Media Manager
	 *
	 * @param $filePath string
	 *
	 * @return string
	 */
	public function onMediaEditorDisplay($filePath)
	{
		JFactory::getDocument()->addScript('/media/plg_media-editor_imagecropper/js/cropper.js');
		JFactory::getDocument()->addStyleSheet('/media/plg_media-editor_imagecropper/css/cropper.min.css');

		$data = array('filePath' => $filePath);
		$layout = new JLayoutFile('form', __DIR__ . '/layout');
		$html = $layout->render($data);

		return $html;
	}

	/**
	 * Method to process the given file
	 *
	 * @param $filePath string
	 *
	 * @return string
	 */
	public function onMediaEditorProcess($fullPath)
	{
		jimport( 'joomla.filesystem.file' );

		// Get data
		$input   	= JFactory::getApplication()->input;

		// Get the dir and filename
		$pathInfo   = pathinfo($fullPath);
		$fileName	= $pathInfo['basename'];
		$filePath	= ($pathInfo['dirname'] != '.' ? $pathInfo['dirname'] . '/' : '');

		$baseMediaPath = JPATH_ROOT . '/images/';

		$jsonData = json_decode($input->get('imagecropper-jsondata', '', 'RAW'));

		// Grab the image
		$image = new JImage($baseMediaPath . $filePath . $fileName);

		// Manipulate the image
		if ($jsonData->scaleY == '-1')
		{
			$image = $image->flip(IMG_FLIP_VERTICAL, true);
		}

		if ($jsonData->scaleX == '-1')
		{
			$image = $image->flip(IMG_FLIP_HORIZONTAL, true);
		}

		if ($jsonData->rotate !== 0)
		{
			$image = $image->rotate($jsonData->rotate);
		}

		$image = $image->crop($jsonData->width, $jsonData->height, $jsonData->x, $jsonData->y);

		// resize the image, disabled for now
		if ($jsonData->scaleX !== 1 || $jsonData->scaleY !== 1)
		{
			$iNewWidth  = $image->getWidth() * $jsonData->scaleX;
			$iNewHeight = $image->getHeight() * $jsonData->scaleY;

			$image 	= $image->resize($iNewWidth, $iNewHeight);
		}

		// change filename is save as copy
		if ($input->get('save_copy') === 'on')
		{
			$random   = JUserHelper::genRandomPassword(3);
			$fileName = JFile::makeSafe($random . '_' . $fileName);
			$fileName = str_replace(' ', '-', $fileName);
		}

		$result = $image->toFile($baseMediaPath . $filePath . $fileName);

		//todo report result to user
		if ($result)
		{
			//JLog::add(JText::_('PLG_MEDIA-EDITOR_IMAGECROPPER_SAVE_SUCCESS'), JLog::INFO);
			//JFactory::getApplication()->enqueueMessage(JText::_('PLG_MEDIA-EDITOR_IMAGECROPPER_SAVE_SUCCESS'), JLog::INFO);
		}
		else
		{
			//JLog::add(JText::_('PLG_MEDIA-EDITOR_IMAGECROPPER_SAVE_SUCCESS'), JLog::ERROR);
			//JFactory::getApplication()->enqueueMessage(JText::_('PLG_MEDIA-EDITOR_IMAGECROPPER_SAVE_SUCCESS'), JLog::ERROR);
		}

		// redirect user to the original image
		$newUrl = JRoute::_('index.php?option=com_media&view=file&view=file&file=' . $fullPath, false);

		return $newUrl;
	}
}
