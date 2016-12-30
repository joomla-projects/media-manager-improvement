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
 * Plugin Interface for the Media Action Plugins (E.g. Crop, Rotate)
 *
 * @since   __DEPLOY_VERSION__
 */
interface MediaActionInterface
{
	/**
	 * Get the translated Plugin Title
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getTitle();

	/**
	 * Get the translated plugin Category String
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getCategory();

	/**
	 * Get the Plugin Icon Class (CSS)
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getIconClass();

	/**
	 * Is the plugin usable in server side batch processing
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function isBatchProcessable();

	/**
	 * List of media file extensions (like jpg) where the plugin is usable
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getMediaFileExtensions();

	/**
	 * Get the HTML of the plugin controls
	 *
	 * @param   string  $filePath  The media file
	 * @param   array   $options    Array of plugin options
	 *
	 * @return  string  HTML to render the plugin
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getControls($filePath, $options = array());

	/**
	 * Process the image - it's in the task of the plugin to save the
	 * changed image
	 *
	 * @param   string  $filePath   The media file object
	 * @param   array   $options    Array of plugin options
	 *
	 * @return  bool
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process($filePath, $options = array());
}
