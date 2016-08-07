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
        JFactory::getDocument()->addStyleDeclaration('.icon-imagecropper:before { content: "\2a"; }');

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
		$returnPath = str_replace(COM_MEDIA_BASE, '', $fullPath);

		// Return the new URL
		return JRoute::_('index.php?option=com_media&view=file&view=file&file=' . $returnPath, false);
	}
}
