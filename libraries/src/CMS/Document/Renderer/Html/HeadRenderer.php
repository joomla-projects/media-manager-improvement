<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Document\Renderer\Html;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Document\DocumentRenderer;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

/**
 * HTML document renderer for the document `<head>` element
 *
 * @since  3.5
 */
class HeadRenderer extends DocumentRenderer
{
	/**
	 * Renders the document head and returns the results as a string
	 *
	 * @param   string  $head     (unused)
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  The script
	 *
	 * @return  string  The output of the script
	 *
	 * @since   3.5
	 */
	public function render($head, $params = array(), $content = null)
	{
		$buffer  = '';
		$buffer .= $this->_doc->loadRenderer('metas')->render($head, $params, $content);
		$buffer .= $this->_doc->loadRenderer('styles')->render($head, $params, $content);
		$buffer .= $this->_doc->loadRenderer('scripts')->render($head, $params, $content);

		return $buffer;
	}
}
