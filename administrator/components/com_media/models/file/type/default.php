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
 * Media Component File model for abstract type
 */
class MediaModelFileTypeDefault implements MediaModelFileTypeInterface
{
	/**
	 * Name of this file type
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $name = 'default';

	/**
	 * File extensions supported by this file type
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $extensions = array();

	/**
	 * MIME types supported by this file type
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $mimeTypes = array();

	/**
	 * Return the name of this file type
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Return the list of supported exensions
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getExtensions()
	{
		return $this->extensions;
	}

	/**
	 * Return the list of supported MIME types
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getMimeTypes()
	{
		return $this->mimeTypes;
	}

	/**
	 * Return the file properties of a specific file
	 *
	 * @param  string  $filePath
	 *
	 * @return  array
	 *
	 * @since   3.6
	 */
	public function getProperties($filePath)
	{
		return array('mime_type' => 'unknown');
	}
}
