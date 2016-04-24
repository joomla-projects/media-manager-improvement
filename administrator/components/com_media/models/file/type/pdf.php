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
 * Media Component File Type Image Model
 */
class MediaModelFileTypePdf extends MediaModelFileTypeDefault implements MediaModelFileTypeInterface
{
	/**
	 * Name of this file type
	 *
	 * @var    string
	 * @since  3.6
	 */
	protected $name = 'pdf';

	/**
	 * File extensions supported by this file type
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $extensions = array(
		'pdf',
	);

	/**
	 * MIME types supported by this file type
	 *
	 * @var    array
	 * @since  3.6
	 */
	protected $mimeTypes = array(
		'application/pdf',
	);

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
		// @todo: Count the number of pages in the PDF
		// @todo: Detect the PDF version type

		return array();
	}
}
