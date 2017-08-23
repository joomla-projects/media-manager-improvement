<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsfeeds
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Newsfeeds\Administrator\Field;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

/**
 * News Feed List field.
 *
 * @since  1.6
 */
class NewsfeedsField extends \JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Newsfeeds';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = array();

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id As value, name As text')
			->from('#__newsfeeds AS a')
			->order('a.name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			\JFactory::getApplication()->enqueueMessage($db->getMessage(), 'error');
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
