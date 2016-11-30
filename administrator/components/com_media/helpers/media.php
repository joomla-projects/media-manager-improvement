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
 * Media helper class.
 *
 * @since       1.6
 */
class MediaHelper extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_MEDIA_SUBMENU_FILES'),
			'index.php?option=com_media&view=files',
			$vName == 'files'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_MEDIA_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_media',
			$vName == 'categories'
		);

		if (JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_media')->get('custom_fields_enable', '1'))
		{
			JHtmlSidebar::addEntry(
					JText::_('JGLOBAL_FIELDS'),
					'index.php?option=com_fields&context=com_media.file',
					$vName == 'fields.article'
					);
			JHtmlSidebar::addEntry(
					JText::_('JGLOBAL_FIELD_GROUPS'),
					'index.php?option=com_categories&extension=com_media.file.fields',
					$vName == 'categories.article'
					);
		}
	}

	/**
	 * Checks if the file is an image
	 *
	 * @param   string  $fileName  The filename
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 * @deprecated  4.0  Use JHelperMedia::isImage instead
	 */
	public static function isImage($fileName)
	{
		JLog::add('MediaHelper::isImage() is deprecated. Use JHelperMedia::isImage() instead.', JLog::WARNING, 'deprecated');
		$mediaHelper = new JHelperMedia;

		return $mediaHelper->isImage($fileName);
	}

	/**
	 * Gets the file extension for the purpose of using an icon.
	 *
	 * @param   string  $fileName  The filename
	 *
	 * @return  string  File extension
	 *
	 * @since   1.5
	 * @deprecated  4.0  Use JHelperMedia::getTypeIcon instead
	 */
	public static function getTypeIcon($fileName)
	{
		JLog::add('MediaHelper::getTypeIcon() is deprecated. Use JHelperMedia::getTypeIcon() instead.', JLog::WARNING, 'deprecated');
		$mediaHelper = new JHelperMedia;

		return $mediaHelper->getTypeIcon($fileName);
	}

	/**
	 * Checks if the file can be uploaded
	 *
	 * @param   array   $file   File information
	 * @param   string  $error  An error message to be returned
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 * @deprecated  4.0  Use JHelperMedia::canUpload instead
	 */
	public static function canUpload($file, $error = '')
	{
		JLog::add('MediaHelper::canUpload() is deprecated. Use JHelperMedia::canUpload() instead.', JLog::WARNING, 'deprecated');
		$mediaHelper = new JHelperMedia;

		return $mediaHelper->canUpload($file, 'com_media');
	}

	/**
	 * Method to parse a file size
	 *
	 * @param   integer  $size  The file size in bytes
	 *
	 * @return  string  The converted file size
	 *
	 * @since   1.6
	 * @deprecated  4.0  Use JHtmlNumber::bytes() instead
	 */
	public static function parseSize($size)
	{
		JLog::add('MediaHelper::parseSize() is deprecated. Use JHtmlNumber::bytes() instead.', JLog::WARNING, 'deprecated');

		return JHtml::_('number.bytes', $size);
	}

	/**
	 * Calculate the size of a resized image
	 *
	 * @param   integer  $width   Image width
	 * @param   integer  $height  Image height
	 * @param   integer  $target  Target size
	 *
	 * @return  array  The new width and height
	 *
	 * @since   3.2
	 * @deprecated  4.0  Use JHelperMedia::imageResize instead
	 */
	public static function imageResize($width, $height, $target)
	{
		JLog::add('MediaHelper::countFiles() is deprecated. Use JHelperMedia::countFiles() instead.', JLog::WARNING, 'deprecated');
		$mediaHelper = new JHelperMedia;

		return $mediaHelper->imageResize($width, $height, $target);
	}

	/**
	 * Counts the files and directories in a directory that are not php or html files.
	 *
	 * @param   string  $dir  Directory name
	 *
	 * @return  array  The number of files and directories in the given directory
	 *
	 * @since   1.5
	 * @deprecated  4.0  Use JHelperMedia::countFiles instead
	 */
	public static function countFiles($dir)
	{
		JLog::add('MediaHelper::countFiles() is deprecated. Use JHelperMedia::countFiles() instead.', JLog::WARNING, 'deprecated');
		$mediaHelper = new JHelperMedia;

		return $mediaHelper->countFiles($dir);
	}
}
