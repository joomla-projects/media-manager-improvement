<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Type.image
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// @todo Move to autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_media/libraries/Joomla/MediaManager/Plugin/MediaType/Plugin.php';

/**
 * Media file type image support
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgMediaTypeImage extends Joomla\MediaManager\Plugin\MediaType\Plugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;

	/**
	 * Supported extensions by plugin
	 *
	 * @var     array
	 * @since   __DEPLOY_VERSION__
	 */
	protected static $extensions = array(
		'jpg',
		'png',
		'gif',
		'bmp',
		'jpeg'
	);

	/**
	 * Render the Layout
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile  The media file
	 *
	 * @return  string  The HTML code (use JLayout if possible)
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function render($mediaFile)
	{
		// Only activate for files we support
		if (!in_array($mediaFile->getFileExtension(), self::$extensions))
		{
			return '';
		}

		// Get the path for the layout file
		$path = JPluginHelper::getLayoutPath('media-type', 'image');

		// Render the image
		ob_start();
		include $path;
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * @param \Joomla\MediaManager\MediaFile $mediaFile
	 *
	 * @return   array  Associative Array (Key => Value) pair of informations
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getProperties($mediaFile)
	{
		// TODO: Implement getProperties() method.
		return array();
	}

	/**
	 * Get media extensions
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getMediaExtensions()
	{
		return self::$extensions;
	}
}
