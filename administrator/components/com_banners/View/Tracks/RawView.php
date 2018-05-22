<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Banners\Administrator\View\Tracks;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die;

/**
 * View class for a list of tracks.
 *
 * @since  1.6
 */
class RawView extends BaseHtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$basename = $this->get('BaseName');
		$filetype = $this->get('FileType');
		$mimetype = $this->get('MimeType');
		$content  = $this->get('Content');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		$document = \JFactory::getDocument();
		$document->setMimeEncoding($mimetype);
		\JFactory::getApplication()
			->setHeader(
				'Content-disposition',
				'attachment; filename="' . $basename . '.' . $filetype . '"; creation-date="' . \JFactory::getDate()->toRFC822() . '"',
				true
			);
		echo $content;
	}
}
