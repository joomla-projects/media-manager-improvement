<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_redirect
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Redirect\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * Redirect component helper.
 *
 * @since  1.6
 */
class RedirectHelper
{
	public static $extension = 'com_redirect';

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		// No submenu for this component.
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array  An array containing the options
	 *
	 * @since   1.6
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = \JHtml::_('select.option', '*', 'JALL');
		$options[] = \JHtml::_('select.option', '1', 'JENABLED');
		$options[] = \JHtml::_('select.option', '0', 'JDISABLED');
		$options[] = \JHtml::_('select.option', '2', 'JARCHIVED');
		$options[] = \JHtml::_('select.option', '-2', 'JTRASHED');

		return $options;
	}

	/**
	 * Gets the redirect system plugin extension id.
	 *
	 * @return  int  The redirect system plugin extension id.
	 *
	 * @since   3.6.0
	 */
	public static function getRedirectPluginId()
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('element') . ' = ' . $db->quote('redirect'));
		$db->setQuery($query);

		try
		{
			$result = (int) $db->loadResult();
		}
		catch (\RuntimeException $e)
		{
			\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Checks whether the option "Collect URLs" is enabled for the output message
	 *
	 * @return  boolean
	 *
	 * @since   3.4
	 */
	public static function collectUrlsEnabled()
	{
		$collect_urls = false;

		if (PluginHelper::isEnabled('system', 'redirect'))
		{
			$params       = new Registry(PluginHelper::getPlugin('system', 'redirect')->params);
			$collect_urls = (bool) $params->get('collect_urls', 1);
		}

		return $collect_urls;
	}
}
