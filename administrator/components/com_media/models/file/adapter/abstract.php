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
 * Media Manager model to abstract file actions
 *
 * @since  3.6
 */
abstract class MediaModelFileAdapterAbstract implements MediaModelFileAdapterInterfaceAdapter
{
	/**
	 * Full path to a file
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $filePath;

	/**
	 * Get the current file path
	 *
	 * @return  string
	 *
	 * @since   3.6
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * Set the current file path
	 *
	 * @param string $filePath
	 *
	 * @return  $this
	 *
	 * @since   3.6
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;

		return $this;
	}
}
