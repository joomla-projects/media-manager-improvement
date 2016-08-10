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
 * Class PlgMediaEditorImagefilters
 *
 * Plugin that can apply filters to images
 */
class PlgMediaEditorImagefilters extends JPlugin
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
		JFactory::getDocument()->addStyleDeclaration('.icon-imagefilters:before { content: "\2a"; }');

		return JText::_('imagefilters'); //PLG_MEDIA-EDITOR_IMAGEFILTERS_BUTTON_LABEL
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
		//todo: allow for setup of cropper parameters

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
		$returnPath = str_replace(COM_MEDIA_BASE, '', $fullPath);

		// Return the new URL
		return JRoute::_('index.php?option=com_media&view=file&view=file&file=' . $returnPath, false);
	}
}
