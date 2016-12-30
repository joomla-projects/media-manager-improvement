<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.rotate
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Media Manager Rotate Action
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgMediaActionRotate extends MediaAction
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
	 * Is the plugin usable in server side batch processing
	 *
	 * @return bool
	 */
	public function isBatchProcessable()
	{
		return true;
	}

	/**
	 * Get the Plugin Title
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getTitle()
	{
		return JText::_('PLG_MEDIA-ACTION_ROTATE');
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
		return JText::_('PLG_MEDIA-ACTION_EDIT');
	}

	/**
	 * Get the Plugin Icon Class
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getIconClass()
	{
		return 'icon-media-rotate';
	}

	/**
	 * Get supported media extensions for this plugin
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getMediaFileExtensions()
	{
		return self::$extensions;
	}

	/**
	 * Get the controls
	 *
	 * @param   string  $filePath  The media file
	 * @param   array   $options   Array of plugin options
	 *
	 * @return  string  HTML to render the plugin
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getControls($filePath, $options = array())
	{
		// @todo Move to jlayout, load JS lib and make it actually useful!
		return '<p><input type="number" min="0" max="360" name="rotate_degree" id="rotate_degree" placeholder="Rotate degree" /></p>';
	}

	/**
	 * Process the image - it's in the task of the plugin to save the
	 * changed image
	 *
	 * @param   string  $filePath  The media file object
	 * @param   array   $options   Array of plugin options
	 *
	 * @return  bool
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process($filePath, $options = array())
	{
		return true;
	}
}
