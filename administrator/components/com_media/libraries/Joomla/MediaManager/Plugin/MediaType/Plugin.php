<?php

namespace Joomla\MediaManager\Plugin\MediaType;

defined('_JEXEC') or die;

/**
 * Plugin Interface for the Media Type Plugins (E.g. Image, PDF)
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Plugin implements PluginInterface
{
	/**
	 * Basic implementation of sanitize the file (returns true)
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile   - The media file objecth
	 *
	 * @return  bool|Exception (true on success / not applicable) Exception
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function sanitizeFile($mediaFile)
	{
		return true;
	}

	/**
	 * Get an Array of File extensions for this Plugin
	 *
	 * @return  array (List of supported extensions)
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getMediaExtensions()
	{
		return array();
	}
}
