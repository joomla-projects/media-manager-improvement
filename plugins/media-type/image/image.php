<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Type.image
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

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
		// TODO: Implement render() method.
		return '<img src="' . $mediaFile->getFileRoute() . '" alt="' . $mediaFile->title . '" title="" />';
	}

	/**
	 * @param \Joomla\MediaManager\MediaFile $mediaFile
	 *
	 * @return   array  Associtive Array (Key => Value) pair of informations
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getProperties($mediaFile)
	{
		// TODO: Implement getProperties() method.
	}
}
