<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Categories;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;

/**
 * Categories Class.
 *
 * @since  1.6
 */
class Categories
{
	/**
	 * Array to hold the object instances
	 *
	 * @var    Categories[]
	 * @since  1.6
	 */
	public static $instances = array();

	/**
	 * Array of category nodes
	 *
	 * @var    CategoryNode[]
	 * @since  1.6
	 */
	protected $_nodes;

	/**
	 * Array of checked categories -- used to save values when _nodes are null
	 *
	 * @var    boolean[]
	 * @since  1.6
	 */
	protected $_checkedCategories;

	/**
	 * Name of the extension the categories belong to
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $_extension = null;

	/**
	 * Name of the linked content table to get category content count
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $_table = null;

	/**
	 * Name of the category field
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $_field = null;

	/**
	 * Name of the key field
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $_key = null;

	/**
	 * Name of the items state field
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $_statefield = null;

	/**
	 * Array of options
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected $_options = [];

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 *
	 * @since   1.6
	 */
	public function __construct($options)
	{
		// Required options
		$this->_extension  = $options['extension'];
		$this->_table      = $options['table'];
		$this->_field      = isset($options['field']) && $options['field'] ? $options['field'] : 'catid';
		$this->_key        = isset($options['key']) && $options['key'] ? $options['key'] : 'id';
		$this->_statefield = $options['statefield'] ?? 'state';

		// Default some optional options
		$this->_options['access']      = 'true';
		$this->_options['published']   = 1;
		$this->_options['countItems']  = 0;
		$this->_options['currentlang'] = Multilanguage::isEnabled() ? Factory::getLanguage()->getTag() : 0;

		$this->setOptions($options);
	}

	/**
	 * Returns a reference to a Categories object
	 *
	 * @param   string  $extension  Name of the categories extension
	 * @param   array   $options    An array of options
	 *
	 * @return  Categories|boolean  Categories object on success, boolean false if an object does not exist
	 *
	 * @since       1.6
	 * @deprecated  5.0 Use the ComponentInterface to get the categories
	 */
	public static function getInstance($extension, $options = array())
	{
		$hash = md5(strtolower($extension) . serialize($options));

		if (isset(self::$instances[$hash]))
		{
			return self::$instances[$hash];
		}

		$parts = explode('.', $extension, 2);

		$categories = Factory::getApplication()->bootComponent($parts[0])->getCategories($options, count($parts) > 1 ? $parts[1] : '');

		self::$instances[$hash] = $categories;

		return self::$instances[$hash];
	}

	/**
	 * Loads a specific category and all its children in a CategoryNode object
	 *
	 * @param   mixed    $id         an optional id integer or equal to 'root'
	 * @param   boolean  $forceload  True to force  the _load method to execute
	 *
	 * @return  CategoryNode|null|boolean  CategoryNode object or null if $id is not valid
	 *
	 * @since   1.6
	 */
	public function get($id = 'root', $forceload = false)
	{
		if ($id !== 'root')
		{
			$id = (int) $id;

			if ($id == 0)
			{
				$id = 'root';
			}
		}

		// If this $id has not been processed yet, execute the _load method
		if ((!isset($this->_nodes[$id]) && !isset($this->_checkedCategories[$id])) || $forceload)
		{
			$this->_load($id);
		}

		// If we already have a value in _nodes for this $id, then use it.
		if (isset($this->_nodes[$id]))
		{
			return $this->_nodes[$id];
		}
		// If we processed this $id already and it was not valid, then return null.
		elseif (isset($this->_checkedCategories[$id]))
		{
			return;
		}

		return false;
	}

	/**
	 * Load method
	 *
	 * @param   integer  $id  Id of category to load
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function _load($id)
	{
		$db   = Factory::getDbo();
		$app  = Factory::getApplication();
		$user = Factory::getUser();
		$extension = $this->_extension;

		// Record that has this $id has been checked
		$this->_checkedCategories[$id] = true;

		$query = $db->getQuery(true);

		// Right join with c for category
		$query->select('c.id, c.asset_id, c.access, c.alias, c.checked_out, c.checked_out_time,
			c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
			c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
			c.path, c.published, c.rgt, c.title, c.modified_user_id, c.version');
		$case_when = ' CASE WHEN ';
		$case_when .= $query->charLength('c.alias', '!=', '0');
		$case_when .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$case_when .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= $c_id . ' END as slug';
		$query->select($case_when)
			->from('#__categories as c')
			->where('(c.extension=' . $db->quote($extension) . ' OR c.extension=' . $db->quote('system') . ')');

		if ($this->_options['access'])
		{
			$query->where('c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
		}

		if ($this->_options['published'] == 1)
		{
			$query->where('c.published = 1');
		}

		$query->order('c.lft');

		// Note: s for selected id
		if ($id != 'root')
		{
			// Get the selected category
			$query->where('s.id=' . (int) $id);

			if ($app->isClient('site') && Multilanguage::isEnabled())
			{
				$query->join('LEFT', '#__categories AS s ON (s.lft < c.lft AND s.rgt > c.rgt AND c.language in (' . $db->quote(Factory::getLanguage()->getTag())
					. ',' . $db->quote('*') . ')) OR (s.lft >= c.lft AND s.rgt <= c.rgt)');
			}
			else
			{
				$query->join('LEFT', '#__categories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)');
			}
		}
		else
		{
			if ($app->isClient('site') && Multilanguage::isEnabled())
			{
				$query->where('c.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			}
		}

		// Note: i for item
		if ($this->_options['countItems'] == 1)
		{
			$queryjoin = $db->quoteName($this->_table) . ' AS i ON i.' . $db->quoteName($this->_field) . ' = c.id';

			if ($this->_options['published'] == 1)
			{
				$queryjoin .= ' AND i.' . $this->_statefield . ' = 1';
			}

			if ($this->_options['currentlang'] !== 0)
			{
				$queryjoin .= ' AND (i.language = ' . $db->quote('*') . ' OR i.language = ' . $db->quote($this->_options['currentlang']) . ')';
			}

			$query->join('LEFT', $queryjoin);
			$query->select('COUNT(i.' . $db->quoteName($this->_key) . ') AS numitems');

			// Group by
			$query->group(
				'c.id, c.asset_id, c.access, c.alias, c.checked_out, c.checked_out_time,
			 c.created_time, c.created_user_id, c.description, c.extension, c.hits, c.language, c.level,
			 c.lft, c.metadata, c.metadesc, c.metakey, c.modified_time, c.note, c.params, c.parent_id,
			 c.path, c.published, c.rgt, c.title, c.modified_user_id, c.version'
			);
		}

		// Get the results
		$db->setQuery($query);
		$results = $db->loadObjectList('id');
		$childrenLoaded = false;

		if (count($results))
		{
			// Foreach categories
			foreach ($results as $result)
			{
				// Deal with root category
				if ($result->id == 1)
				{
					$result->id = 'root';
				}

				// Deal with parent_id
				if ($result->parent_id == 1)
				{
					$result->parent_id = 'root';
				}

				// Create the node
				if (!isset($this->_nodes[$result->id]))
				{
					// Create the CategoryNode and add to _nodes
					$this->_nodes[$result->id] = new CategoryNode($result, $this);

					// If this is not root and if the current node's parent is in the list or the current node parent is 0
					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id == 1))
					{
						// Compute relationship between node and its parent - set the parent in the _nodes field
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
				elseif ($result->id == $id || $childrenLoaded)
				{
					// Create the CategoryNode
					$this->_nodes[$result->id] = new CategoryNode($result, $this);

					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id))
					{
						// Compute relationship between node and its parent
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0))
					{
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded)
					{
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
			}
		}
		else
		{
			$this->_nodes[$id] = null;
		}
	}

	/**
	 * Allows to set some optional options, eg. if the access level should be considered.
	 * Also clears the internal children cache.
	 *
	 * @param   array  $options  The new options
	 *
	 * @return  void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setOptions(array $options)
	{
		if (isset($options['access']))
		{
			$this->_options['access'] = $options['access'];
		}

		if (isset($options['published']))
		{
			$this->_options['published'] = $options['published'];
		}

		if (isset($options['countItems']))
		{
			$this->_options['countItems'] = $options['countItems'];
		}

		// Reset the cache
		$this->_nodes             = [];
		$this->_checkedCategories = [];
	}
}
