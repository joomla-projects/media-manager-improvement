<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_multilangstatus
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\Multilangstatus\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;

/**
 * Helper class for the multilangstatus module
 *
 * @since  4.0.0
 */
class MultilangstatusAdminHelper
{
	/**
	 * Method to check if the module exists and is enabled as extension
	 *
	 * @return  boolean
	 *
	 * @since   4.0.0
	 */
	public static function isEnabled()
	{
		$app   = Factory::getApplication();
		$db    = Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('enabled'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote('module'))
			->where($db->quoteName('element') . ' = ' . $db->quote('mod_multilangstatus'));
		$db->setQuery($query);

		try
		{
			$result = (int) $db->loadResult();
		}
		catch (\RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Method to check the state of the module
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public static function getState()
	{
		$app   = Factory::getApplication();
		$db    = Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('published'))
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('module') . ' = ' . $db->quote('mod_multilangstatus'));
		$db->setQuery($query);

		try
		{
			$result = (int) $db->loadResult();
		}
		catch (\RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Method to publish/unpublish the module depending on the languagefilter state
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public static function publish()
	{
		$app   = Factory::getApplication();
		$db    = Factory::getDbo();

		// If the module is trashed do not change its status
		if (self::getState() != -2)
		{
			// Publish the module when the languagefilter is enabled
			if (Multilanguage::isEnabled())
			{
				$query = $db->getQuery(true)
					->update($db->quoteName('#__modules'))
					->set($db->quoteName('published') . ' = ' . (int) 1)
					->where($db->quoteName('module') . ' = ' . $db->quote('mod_multilangstatus'));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (\RuntimeException $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');

					return;
				}
			}
			else
			{
				// Unpublish the module when the languagefilter is disabled
				$query = $db->getQuery(true)
					->update($db->quoteName('#__modules'))
					->set($db->quoteName('published') . ' = ' . (int) 0)
					->where($db->quoteName('module') . ' = ' . $db->quote('mod_multilangstatus'));

				try
				{
					$db->setQuery($query)->execute();
				}
				catch (Exception $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');

					return;
				}
			}
		}
	}
}
