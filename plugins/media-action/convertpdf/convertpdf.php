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
require_once JPATH_ADMINISTRATOR . '/components/com_media/libraries/Joomla/MediaManager/Plugin/Action/Plugin.php';

/**
 * Convert images and other supported files to PDF
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgMediaActionConvertpdf extends Joomla\MediaManager\Plugin\Action\Plugin
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
	 * Get the Plugin Title
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getTitle()
	{
		return JText::_('PLG_MEDIA-ACTION_CONVERT');
	}

	/**
	 * Get the Plugin Category String
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCategory()
	{
		return JText::_('PLG_MEDIA-ACTION_CONVERT');
	}

	/**
	 * Get the Plugin Icon Class
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCssClass()
	{
		return 'icon-media-crop';
	}

	/**
	 * Get the controls to render in the backend
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile  The media file object
	 *
	 * @return  string  HTML to render
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getControls($mediaFile)
	{
		// @todo Move to jlayout, load JS lib and make it actually useful!
		return '<p><input type="number" min="0" max="100" name="pdf_quality" id="pdf_quality" placeholder="PDF Quality" /></p>';

	}

	/**
	 * Process the image - it's in the task of the plugin to save the
	 * changed image
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile  The media file object
	 *
	 * @return  boolean  True if changes are made
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process($mediaFile)
	{
		return true;
	}

	/**
	 * Get supported media extensions for this plugin
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
