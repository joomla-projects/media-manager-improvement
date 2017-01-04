<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.filter
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Media Manager Filter Action
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgMediaActionFilter extends MediaAction
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
		return JText::_('PLG_MEDIA-ACTION_FILTER_TITLE');
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
		return JText::_('PLG_MEDIA-ACTION_FILTER');
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
		return 'palette';
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
		$layout = new JLayoutFile('controls', __DIR__ . '/layouts');

		return $layout->render(array($filePath, $options));
	}

	/**
	 * Process the media file
	 *
	 * @param   Resource  $resource  The media resource (Image etc)
	 * @param   array     $options   Array of plugin options
	 *
	 * @return  Resource  The manipulated file
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process($resource, $options = array())
	{
		$brightness = $options['brightness'];
		$contrast = $options['contrast'];

		// @todo Transparency, error handling etc
		imagefilter($resource, IMG_FILTER_BRIGHTNESS, $brightness);
		imagefilter($resource, IMG_FILTER_CONTRAST, $contrast);

		return $resource;
	}
}
