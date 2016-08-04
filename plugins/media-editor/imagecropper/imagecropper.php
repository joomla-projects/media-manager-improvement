<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  MediaEditor.Imagecropper
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Class PlgMediaEditorImagecropper
 *
 * Plugin that can rotate, resize and crop images
 *
 * @since  3.7.0
 */
class PlgMediaEditorImagecropper extends JPlugin
{
	/**
	 * The application.
	 *
	 * @var    JApplicationAdministrator
	 * @since  3.7.0
	 */
	 protected $app;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   3.7.0
	 *
	 * @throws  Exception
	 */
	public function __construct(&$subject, $config = array())
	{
		// Auto-load the language
		$this->autoloadLanguage = true;

		parent::__construct($subject, $config);
	}

	/**
	 * Method to check whether this media editor plugin is allowed on a specific fileType
	 *
	 * @param $fileType string
	 *
	 * @return bool
	 *
	 * @since  3.7.0
	 */
	public function onMediaEditorAllowed($fileType)
	{
		return $fileType === 'image';
	}

	/**
	 * Method to return the button label of this plugin
	 *
	 * @return string
	 *
	 * @since  3.7.0
	 */
	public function onMediaEditorButtonLabel()
	{
        $doc = JFactory::getDocument();
        $doc->addStyleDeclaration('.icon-imagecropper:before { content: "\2a"; }');

		return JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_BUTTON_LABEL');
	}

	/**
	 * Method to return the HTML shown in a modal popup within the Media Manager
	 *
	 * @param   string  $filePath The path of the image.
	 *
	 * @return  string  HTML data
	 *
	 * @since   3.7.0
	 *
	 * @throws  InvalidArgumentException
	 */
	public function onMediaEditorDisplay($filePath)
	{
		JHtml::_('script', 'plg_media-editor_imagecropper/cropper.js', false, true);
		JHtml::_('stylesheet', 'plg_media-editor_imagecropper/cropper.css', array(), true);

		//todo: allow for setup of cropper parameters

		$data = array('filePath' => $filePath);
		$layout = new JLayoutFile('form', __DIR__ . '/layout');

		return $layout->render($data);
	}

	/**
	 * Method to process the given file
	 *
	 * @param  string  $fullPath  The full path to the image.
	 *
	 * @return string
	 *
	 * @since  3.7.0
	 *
	 * @throws RuntimeException
	 * @throws LogicException
	 */
	public function onMediaEditorProcess($fullPath)
	{
		jimport('joomla.filesystem.file');

		// Get data
		$input   	= $this->app->input;

		// Get the dir and filename
		$pathInfo   = pathinfo($fullPath);
		$fileName	= $pathInfo['basename'];
		$filePath	= ($pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '');

        $mediaPath  = pathinfo($input->get('file', '', 'RAW'));
        $mediaPath  = ($mediaPath['dirname'] !== '.' ? $mediaPath['dirname'] . '/' : '');

		$jsonData = json_decode($input->get('imagecropper-jsondata', '', 'RAW'));

		// Grab the image
		$image = new JImage($filePath . $fileName);

		// Manipulate the image
		if ($jsonData->scaleY === '-1')
		{
			$image = $image->flip(IMG_FLIP_VERTICAL, true);
		}

		if ($jsonData->scaleX === '-1')
		{
			$image = $image->flip(IMG_FLIP_HORIZONTAL, true);
		}

		if ($jsonData->rotate !== 0)
		{
			$image = $image->rotate($jsonData->rotate);
		}

		/** @var JImage $image */
		$image = $image->crop($jsonData->width, $jsonData->height, $jsonData->x, $jsonData->y);

		// Resize the image, disabled for now
		if ($jsonData->scaleX !== 1 || $jsonData->scaleY !== 1)
		{
			$iNewWidth  = $image->getWidth() * $jsonData->scaleX;
			$iNewHeight = $image->getHeight() * $jsonData->scaleY;

			$image 	= $image->resize($iNewWidth, $iNewHeight);
		}

		// Change filename is save as copy
		if ($input->get('save_copy') === 'on')
		{
			$random   = JUserHelper::genRandomPassword(3);
			$fileName = JFile::makeSafe($random . '_' . $fileName);
			$fileName = str_replace(' ', '-', $fileName);
		}

		$result = $image->toFile($filePath . $fileName);

		/** @var MediaModelFile $model */
		$model = JModelLegacy::getInstance('File', 'MediaModel');
		$model->loadByPath($filePath . $fileName);

		//todo report result to user
		if ($result)
		{
			//JLog::add(JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_SAVE_SUCCESS'), JLog::INFO);
			//JFactory::getApplication()->enqueueMessage(JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_SAVE_SUCCESS'), JLog::INFO);
		}
		else
		{
			//JLog::add(JText::_('PLG_MEDIA-EDITOR_IMAGECROPPER_SAVE_FAILURE'), JLog::ERROR);
			//JFactory::getApplication()->enqueueMessage(JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_SAVE_FAILURE'), JLog::ERROR);
		}

		// redirect user to the image
		$newUrl = JRoute::_('index.php?option=com_media&view=file&view=file&file=' . $mediaPath . $fileName, false);

		return $newUrl;
	}
}
