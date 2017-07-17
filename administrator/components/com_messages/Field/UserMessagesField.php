<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Messages\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('user');

/**
 * Supports a modal select of users that have access to com_messages
 *
 * @since  1.6
 */
class UserMessagesField extends \JFormFieldUser
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	public $type = 'UserMessages';

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  array|null	array of filtering groups or null.
	 *
	 * @since   1.6
	 */
	protected function getGroups()
	{
		// Compute usergroups
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from('#__usergroups');
		$db->setQuery($query);

		try
		{
			$groups = $db->loadColumn();
		}
		catch (\RuntimeException $e)
		{
			\JError::raiseNotice(500, $e->getMessage());

			return null;
		}

		foreach ($groups as $i => $group)
		{
			if (Access::checkGroup($group, 'core.admin'))
			{
				continue;
			}

			if (!Access::checkGroup($group, 'core.manage', 'com_messages'))
			{
				unset($groups[$i]);
				continue;
			}

			if (!Access::checkGroup($group, 'core.login.admin'))
			{
				unset($groups[$i]);
				continue;
			}
		}

		return array_values($groups);
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  array|null array of users to exclude or null to to not exclude them
	 *
	 * @since   1.6
	 */
	protected function getExcluded()
	{
		return array(Factory::getUser()->id);
	}
}
