<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_latest
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\Latest\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\Component\Content\Administrator\Model\Articles;
use Joomla\Registry\Registry;

/**
 * Helper for mod_latest
 *
 * @since  1.5
 */
abstract class ModLatestHelper
{
	/**
	 * Get a list of articles.
	 *
	 * @param   Registry  &$params  The module parameters.
	 * @param   Articles  $model    The model.
	 *
	 * @return  mixed  An array of articles, or false on error.
	 */
	public static function getList(Registry &$params, Articles $model)
	{
		$user = \JFactory::getUser();

		// Set List SELECT
		$model->setState('list.select', 'a.id, a.title, a.checked_out, a.checked_out_time, ' .
			' a.access, a.created, a.created_by, a.created_by_alias, a.featured, a.state, a.publish_up, a.publish_down');

		// Set Ordering filter
		switch ($params->get('ordering'))
		{
			case 'm_dsc':
				$model->setState('list.fullordering', 'modified DESC, created');
				$model->setState('list.direction', 'DESC');
				break;

			case 'c_dsc':
			default:
				$model->setState('list.fullordering', 'created DESC');
				$model->setState('list.direction', 'DESC');
				break;
		}

		// Set Category Filter
		$categoryId = $params->get('catid');

		if (is_numeric($categoryId))
		{
			$model->setState('filter.category_id', $categoryId);
		}

		// Set User Filter.
		$userId = $user->get('id');

		switch ($params->get('user_id'))
		{
			case 'by_me':
				$model->setState('filter.author_id', $userId);
				break;

			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;
		}

		// Set the Start and Limit
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('count', 5));

		$items = $model->getItems();

		if ($error = $model->getError())
		{
			\JError::raiseError(500, $error);

			return false;
		}

		// Set the links
		foreach ($items as &$item)
		{
			if ($user->authorise('core.edit', 'com_content.article.' . $item->id))
			{
				$item->link = \JRoute::_('index.php?option=com_content&task=article.edit&id=' . $item->id);
			}
			else
			{
				$item->link = '';
			}
		}

		return $items;
	}

	/**
	 * Get the alternate title for the module.
	 *
	 * @param   \Joomla\Registry\Registry  $params  The module parameters.
	 *
	 * @return  string  The alternate title for the module.
	 */
	public static function getTitle($params)
	{
		$who   = $params->get('user_id');
		$catid = (int) $params->get('catid');
		$type  = $params->get('ordering') == 'c_dsc' ? '_CREATED' : '_MODIFIED';

		if ($catid)
		{
			$category = \JCategories::getInstance('Content')->get($catid);

			if ($category)
			{
				$title = $category->title;
			}
			else
			{
				$title = \JText::_('MOD_POPULAR_UNEXISTING');
			}
		}
		else
		{
			$title = '';
		}

		return \JText::plural('MOD_LATEST_TITLE' . $type . ($catid ? '_CATEGORY' : '') . ($who != '0' ? "_$who" : ''), (int) $params->get('count'), $title);
	}
}
