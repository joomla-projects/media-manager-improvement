<?php

namespace Joomla\MediaManager\Plugin\MediaType;

defined('_JEXEC') or die;

/**
 * Media file which represents a database entry and offers handy functions to work with
 *
 * @since   __DEPLOY_VERSION__
 */
class MediaFile extends \MediaTableFile
{
	/**
	 * Returns the file extension of the file.
	 *
	 * @return string  The file extension of the file
	 */
	public function getFileExtension()
	{
		return \JFile::getExt($this->filename);
	}

	/**
	 * Returns the full local file path.
	 *
	 * @return string  The full local file path
	 */
	public function getFilePath()
	{
		return JPATH_ROOT . \JPath::clean($this->path . '/' . $this->uuid . '.' . $this->getFileExtension());
	}

	/**
	 * Returns a route accessing the file. If an access flag is set then a the url
	 * points to a media manager route.
	 *
	 * @return string  The route fro frontend access
	 */
	public function getFileRoute()
	{
		if ($this->access)
		{
			// @todo add slug
			return JRoute::_('index.php?option=com_media&task=file.show&format=raw');
		}
		else
		{
			// @todo add md5 check
			$path = JPATH_ROOT . \JPath::clean($this->path . '/' . $this->filename);

			if (!\JFile::exists($path))
			{
				\JFile::copy($this->getFilePath(), $path);
			}
			return $path;
		}

	}
}
