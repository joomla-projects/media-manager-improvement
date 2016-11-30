<?php

namespace Joomla\MediaManager\Plugin\MediaType;

defined('_JEXEC') or die;

/**
 * Plugin Interface for the Media Type Plugins (E.g. Image, PDF)
 *
 * @since   __DEPLOY_VERSION__
 */
interface PluginInterface
{
	/**
	 * Render the HTML of the Plugin (with JLayout)
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile  The media file object
	 *
	 * @return  mixed
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function render($mediaFile);

	/**
	 * Get the properties of the media element
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile   - The media file object
	 *
	 * @return  mixed
	 *
	 * @return   array  Associative Array (Key -> Value (String|Array))
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getProperties($mediaFile);

	/**
	 * Sanitize the file
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile   - The media file objecth
	 *
	 * @return  bool|Exception (true on success / not applicable) Exception
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function sanitizeFile($mediaFile);

	/**
	 * Get an Array of File extensions for this Plugin
	 *
	 * @return  array (List on)
	 *$fileExt, $filePath
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getMediaExtensions();
}
