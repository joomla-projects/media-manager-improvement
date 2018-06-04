<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Languages\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Component\Languages\Administrator\Helper\LanguagesHelper;

/**
 * Languages Strings Model
 *
 * @since  2.5
 */
class StringsModel extends BaseDatabaseModel
{
	/**
	 * Method for refreshing the cache in the database with the known language strings.
	 *
	 * @return  boolean|\Exception  True on success, \Exception object otherwise.
	 *
	 * @since		2.5
	 */
	public function refresh()
	{
		$app = \JFactory::getApplication();
		$db  = $this->getDbo();

		$app->setUserState('com_languages.overrides.cachedtime', null);

		// Empty the database cache first.
		try
		{
			$db->truncateTable('#__overrider');
		}
		catch (\RuntimeException $e)
		{
			return $e;
		}

		// Create the insert query.
		$query = $db->getQuery(true)
			->insert($db->quoteName('#__overrider'))
			->columns('constant, string, file');

		// Initialize some variables.
		$client   = $app->getUserState('com_languages.overrides.filter.client', 'site') ? 'administrator' : 'site';
		$language = $app->getUserState('com_languages.overrides.filter.language', 'en-GB');

		$base = constant('JPATH_' . strtoupper($client));
		$path = $base . '/language/' . $language;

		$files = array();

		// Parse common language directory.
		jimport('joomla.filesystem.folder');

		if (is_dir($path))
		{
			$files = \JFolder::files($path, $language . '.*ini$', false, true);
		}

		// Parse language directories of components.
		$files = array_merge($files, \JFolder::files($base . '/components', $language . '.*ini$', 3, true));

		// Parse language directories of modules.
		$files = array_merge($files, \JFolder::files($base . '/modules', $language . '.*ini$', 3, true));

		// Parse language directories of templates.
		$files = array_merge($files, \JFolder::files($base . '/templates', $language . '.*ini$', 3, true));

		// Parse language directories of plugins.
		$files = array_merge($files, \JFolder::files(JPATH_PLUGINS, $language . '.*ini$', 3, true));

		// Parse all found ini files and add the strings to the database cache.
		foreach ($files as $file)
		{
			$strings = LanguagesHelper::parseFile($file);

			if ($strings && count($strings))
			{
				$query->clear('values');

				foreach ($strings as $key => $string)
				{
					$query->values($db->quote($key) . ',' . $db->quote($string) . ',' . $db->quote(\JPath::clean($file)));
				}

				try
				{
					$db->setQuery($query);
					$db->execute();
				}
				catch (\RuntimeException $e)
				{
					return $e;
				}
			}
		}

		// Update the cached time.
		$app->setUserState('com_languages.overrides.cachedtime.' . $client . '.' . $language, time());

		return true;
	}

	/**
	 * Method for searching language strings.
	 *
	 * @return  array|\Exception  Array of resuls on success, \Exception object otherwise.
	 *
	 * @since		2.5
	 */
	public function search()
	{
		$results = array();
		$input   = \JFactory::getApplication()->input;
		$filter  = \JFilterInput::getInstance();
		$db      = $this->getDbo();
		$searchTerm = $input->getString('searchstring');

		$limitstart = $input->getInt('more');

		try
		{
			$searchstring = $db->quote('%' . $filter->clean($searchTerm, 'TRIM') . '%');

			// Create the search query.
			$query = $db->getQuery(true)
				->select('constant, string, file')
				->from($db->quoteName('#__overrider'));

			if ($input->get('searchtype') == 'constant')
			{
				$query->where('constant LIKE ' . $searchstring);
			}
			else
			{
				$query->where('string LIKE ' . $searchstring);
			}

			// Consider the limitstart according to the 'more' parameter and load the results.
			$db->setQuery($query, $limitstart, 10);
			$results['results'] = $db->loadObjectList();

			// Check whether there are more results than already loaded.
			$query->clear('select')->clear('limit')
				->select('COUNT(id)');
			$db->setQuery($query);

			if ($db->loadResult() > $limitstart + 10)
			{
				// If this is set a 'More Results' link will be displayed in the view.
				$results['more'] = $limitstart + 10;
			}
		}
		catch (\RuntimeException $e)
		{
			return $e;
		}

		return $results;
	}
}
