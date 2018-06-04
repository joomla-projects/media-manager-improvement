<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Templates\Administrator\Service\HTML;

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Html helper class.
 *
 * @since  1.6
 */
class Templates
{
	/**
	 * Display the thumb for the template.
	 *
	 * @param   string   $template  The name of the template.
	 * @param   integer  $clientId  The application client ID the template applies to
	 *
	 * @return  string  The html string
	 *
	 * @since   1.6
	 */
	public function thumb($template, $clientId = 0)
	{
		$client = ApplicationHelper::getClientInfo($clientId);
		$basePath = $client->path . '/templates/' . $template;
		$thumb = $basePath . '/template_thumbnail.png';
		$preview = $basePath . '/template_preview.png';
		$html = '';

		if (file_exists($thumb))
		{
			$clientPath = ($clientId == 0) ? '' : 'administrator/';
			$thumb = $clientPath . 'templates/' . $template . '/template_thumbnail.png';
			$html = HTMLHelper::_('image', $thumb, Text::_('COM_TEMPLATES_PREVIEW'));

			if (file_exists($preview))
			{
				$html = '<a href="#' . $template . '-Modal" role="button" class="thumbnail float-left hasTooltip" data-toggle="modal" title="' .
					HTMLHelper::_('tooltipText', 'COM_TEMPLATES_CLICK_TO_ENLARGE') . '">' . $html . '</a>';
			}
		}

		return $html;
	}

	/**
	 * Renders the html for the modal linked to thumb.
	 *
	 * @param   string   $template  The name of the template.
	 * @param   integer  $clientId  The application client ID the template applies to
	 *
	 * @return  string  The html string
	 *
	 * @since   3.4
	 */
	public function thumbModal($template, $clientId = 0)
	{
		$client = ApplicationHelper::getClientInfo($clientId);
		$basePath = $client->path . '/templates/' . $template;
		$baseUrl = ($clientId == 0) ? Uri::root(true) : Uri::root(true) . '/administrator';
		$thumb = $basePath . '/template_thumbnail.png';
		$preview = $basePath . '/template_preview.png';
		$html = '';

		if (file_exists($thumb) && file_exists($preview))
		{
			$preview = $baseUrl . '/templates/' . $template . '/template_preview.png';
			$footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
				. Text::_('JTOOLBAR_CLOSE') . '</button>';

			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				$template . '-Modal',
				array(
					'title'  => Text::_('COM_TEMPLATES_BUTTON_PREVIEW'),
					'height' => '500px',
					'width'  => '800px',
					'footer' => $footer,
				),
				$body = '<div><img src="' . $preview . '" style="max-width:100%" alt="' . $template . '"></div>'
			);
		}

		return $html;
	}
}
