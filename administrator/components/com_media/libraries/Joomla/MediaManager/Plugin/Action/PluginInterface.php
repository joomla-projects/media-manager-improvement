<?php

namespace Joomla\MediaManager\Plugin\Action;

defined('_JEXEC') or die;

/**
 * Plugin Interface for the Media Action Plugins (E.g. Crop, Rotate)
 *
 * @since   __DEPLOY_VERSION__
 */
interface PluginInterface
{
	/**
	 * Get the Plugin Title
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getTitle();

	/**
	 * Get the Plugin Category String
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
	public function getCssClass();

	/**
	 * Get the controls
	 *
 	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile  The media file object
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getControls($mediaFile);

	/**
	 * Process the image - it's in the task of the plugin to save the
	 * changed image
	 *
	 * @param   \Joomla\MediaManager\MediaFile  $mediaFile  The media file object
	 *
	 * @return  bool  True if changes are made
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function process($mediaFile);
}
