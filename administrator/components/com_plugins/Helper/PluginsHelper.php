<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Plugins\Administrator\Helper;

defined('_JEXEC') or die;

/**
 * Plugins component helper.
 *
 * @since  1.6
 */
class PluginsHelper
{
	public static $extension = 'com_plugins';

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		// No submenu for this component.
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  string    The HTML code for the select tag
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options = array();
		$options[] = \JHtml::_('select.option', '1', 'JENABLED');
		$options[] = \JHtml::_('select.option', '0', 'JDISABLED');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  string    The HTML code for the select tag
	 */
	public static function folderOptions()
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT(folder) AS value, folder AS text')
			->from('#__extensions')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->order('folder');

		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $options;
	}

	/**
	 * Parse the template file.
	 *
	 * @param   string  $templateBaseDir  Base path to the template directory.
	 * @param   string  $templateDir      Template directory.
	 *
	 * @return  \JObject
	 */
	public function parseXMLTemplateFile($templateBaseDir, $templateDir)
	{
		$data = new \JObject;

		// Check of the xml file exists.
		$filePath = \JPath::clean($templateBaseDir . '/templates/' . $templateDir . '/templateDetails.xml');

		if (is_file($filePath))
		{
			$xml = \JInstaller::parseXMLInstallFile($filePath);

			if ($xml['type'] != 'template')
			{
				return false;
			}

			foreach ($xml as $key => $value)
			{
				$data->set($key, $value);
			}
		}

		return $data;
	}
}
