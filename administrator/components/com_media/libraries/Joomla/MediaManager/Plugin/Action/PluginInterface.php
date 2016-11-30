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
	 * @since   __DEPLOY_VERSION_
	 */
	public function getTitle();

	/**
	 * Get the Plugin Icon Class (CSS)
	 *
	 * @return  string
	 *
	 * @since   __DEPLOY_VERSION_
	 */
	public function getCssClass();

	/**
	 * Process the image - it's in the task of the plugin to save the
	 * changed image
	 *
	 * @return  bool  True if changes are made
	 *
	 * @since   __DEPLOY_VERSION_
	 */
	public function process($mediaFile);
}
