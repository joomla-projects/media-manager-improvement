<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_newsfeeds
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;


/**
 * Routing class from com_newsfeeds
 *
 * @since  3.3
 */
class NewsfeedsRouter extends RouterView
{
	protected $noIDs = false;

	/**
	 * Newsfeeds Component router constructor
	 *
	 * @param   CMSApplication  $app   The application object
	 * @param   AbstractMenu    $menu  The menu object to work with
	 */
	public function __construct($app = null, $menu = null)
	{
		$params = ComponentHelper::getParams('com_newsfeeds');
		$this->noIDs = (bool) $params->get('sef_ids');
		$categories = new RouterViewConfiguration('categories');
		$categories->setKey('id');
		$this->registerView($categories);
		$category = new RouterViewConfiguration('category');
		$category->setKey('id')->setParent($categories, 'catid')->setNestable();
		$this->registerView($category);
		$newsfeed = new RouterViewConfiguration('newsfeed');
		$newsfeed->setKey('id')->setParent($category, 'catid');
		$this->registerView($newsfeed);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	/**
	 * Method to get the segment(s) for a category
	 *
	 * @param   string  $id     ID of the category to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getCategorySegment($id, $query)
	{
		$category = \JCategories::getInstance($this->getName())->get($id);
		if ($category)
		{
			$path = array_reverse($category->getPath(), true);
			$path[0] = '1:root';

			if ($this->noIDs)
			{
				foreach ($path as &$segment)
				{
					list($id, $segment) = explode(':', $segment, 2);
				}
			}

			return $path;
		}

		return array();
	}

	/**
	 * Method to get the segment(s) for a category
	 *
	 * @param   string  $id     ID of the category to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getCategoriesSegment($id, $query)
	{
		return $this->getCategorySegment($id, $query);
	}

	/**
	 * Method to get the segment(s) for a newsfeed
	 *
	 * @param   string  $id     ID of the newsfeed to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getNewsfeedSegment($id, $query)
	{
		if (!strpos($id, ':'))
		{
			$db = \JFactory::getDbo();
			$dbquery = $db->getQuery(true);
			$dbquery->select($dbquery->qn('alias'))
				->from($dbquery->qn('#__newsfeeds'))
				->where('id = ' . $dbquery->q((int) $id));
			$db->setQuery($dbquery);

			$id .= ':' . $db->loadResult();
		}

		if ($this->noIDs)
		{
			list($void, $segment) = explode(':', $id, 2);

			return array($void => $segment);
		}

		return array((int) $id => $id);
	}

	/**
	 * Method to get the id for a category
	 *
	 * @param   string  $segment  Segment to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getCategoryId($segment, $query)
	{
		if (isset($query['id']))
		{
			$category = \JCategories::getInstance($this->getName(), array('access' => false))->get($query['id']);

			if ($category)
			{
				foreach ($category->getChildren() as $child)
				{
					if ($this->noIDs)
					{
						if ($child->alias === $segment)
						{
							return $child->id;
						}
					}
					else
					{
						if ($child->id == (int) $segment)
						{
							return $child->id;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Method to get the segment(s) for a category
	 *
	 * @param   string  $segment  Segment to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getCategoriesId($segment, $query)
	{
		return $this->getCategoryId($segment, $query);
	}

	/**
	 * Method to get the segment(s) for a newsfeed
	 *
	 * @param   string  $segment  Segment of the newsfeed to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getNewsfeedId($segment, $query)
	{
		if ($this->noIDs)
		{
			$db = \JFactory::getDbo();
			$dbquery = $db->getQuery(true);
			$dbquery->select($dbquery->qn('id'))
				->from($dbquery->qn('#__newsfeeds'))
				->where('alias = ' . $dbquery->q($segment))
				->where('catid = ' . $dbquery->q($query['id']));
			$db->setQuery($dbquery);

			return (int) $db->loadResult();
		}

		return (int) $segment;
	}
}
