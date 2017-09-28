<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Module\Menu\Administrator\Menu;

defined('_JEXEC') or die;

use Joomla\Component\Menus\Administrator\Helper\MenusHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Menu\Node;
use Joomla\CMS\Menu\Tree;
use Joomla\CMS\Menu\MenuHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use JText;

/**
 * Tree based class to render the admin menu
 *
 * @since  1.5
 */
class CssMenu
{
	/**
	 * The Menu tree object
	 *
	 * @var   Tree
	 *
	 * @since   3.8.0
	 */
	protected $tree;

	/**
	 * The module options
	 *
	 * @var   Registry
	 *
	 * @since   3.8.0
	 */
	protected $params;

	/**
	 * The menu bar state
	 *
	 * @var   bool
	 *
	 * @since   3.8.0
	 */
	protected $enabled;

	/**
	 * Get the current menu tree
	 *
	 * @return  Tree
	 *
	 * @since   3.8.0
	 */
	public function getTree()
	{
		if (!$this->tree)
		{
			$this->tree = new Tree;
		}

		return $this->tree;
	}

	/**
	 * Populate the menu items in the menu tree object
	 *
	 * @param   Registry  $params   Menu configuration parameters
	 * @param   bool      $enabled  Whether the menu should be enabled or disabled
	 *
	 * @return  void
	 *
	 * @since   3.7.0
	 */
	public function load($params, $enabled)
	{
		$this->tree    = $this->getTree();
		$this->params  = $params;
		$this->enabled = $enabled;
		$menutype      = $this->params->get('menutype', '*');

		if ($menutype == '*')
		{
			$name   = $this->params->get('preset', 'joomla');
			$levels = MenuHelper::loadPreset($name);
		}
		else
		{
			$items = MenusHelper::getMenuItems($menutype, true);

			if ($this->enabled && $this->params->get('check'))
			{
				if ($this->check($items, $this->params))
				{
					$this->params->set('recovery', true);

					// In recovery mode, load the preset inside a special root node.
					$this->tree->addChild(new Node\Heading('MOD_MENU_RECOVERY_MENU_ROOT'), true);

					$levels = MenuHelper::loadPreset('joomla');
					$levels = $this->preprocess($levels);

					$this->populateTree($levels);

					$this->tree->addChild(new Node\Separator);

					// Add link to exit recovery mode
					$uri = clone Uri::getInstance();
					$uri->setVar('recover_menu', 0);

					$this->tree->addChild(new Node\Url('MOD_MENU_RECOVERY_EXIT', $uri->toString()));

					$this->tree->getParent();
				}
			}

			$levels = MenuHelper::createLevels($items);
		}

		$levels = $this->preprocess($levels);

		$this->populateTree($levels);
	}

	/**
	 * Method to render a given level of a menu using provided layout file
	 *
	 * @param   string  $layoutFile  The layout file to be used to render
	 *
	 * @return  void
	 *
	 * @since   3.8.0
	 */
	public function renderSubmenu($layoutFile)
	{
		if (is_file($layoutFile))
		{
			$children = $this->tree->getCurrent()->getChildren();

			foreach ($children as $child)
			{
				$this->tree->setCurrent($child);

				// This sets the scope to this object for the layout file and also isolates other `include`s
				require $layoutFile;
			}
		}
	}

	/**
	 * Check the flat list of menu items for important links
	 *
	 * @param   array     $items   The menu items array
	 * @param   Registry  $params  Module options
	 *
	 * @return  bool  Whether to show recovery menu
	 *
	 * @since   3.8.0
	 */
	protected function check($items, Registry $params)
	{
		$me          = Factory::getUser();
		$authMenus   = $me->authorise('core.manage', 'com_menus');
		$authModules = $me->authorise('core.manage', 'com_modules');

		if (!$authMenus && !$authModules)
		{
			return false;
		}

		$app        = Factory::getApplication();
		$types      = ArrayHelper::getColumn($items, 'type');
		$elements   = ArrayHelper::getColumn($items, 'element');
		$rMenu      = $authMenus && !in_array('com_menus', $elements);
		$rModule    = $authModules && !in_array('com_modules', $elements);
		$rContainer = !in_array('container', $types);

		if ($rMenu || $rModule || $rContainer)
		{
			$recovery = $app->getUserStateFromRequest('mod_menu.recovery', 'recover_menu', 0, 'int');

			if ($recovery)
			{
				return true;
			}

			$missing = array();

			if ($rMenu)
			{
				$missing[] = JText::_('MOD_MENU_IMPORTANT_ITEM_MENU_MANAGER');
			}

			if ($rModule)
			{
				$missing[] = JText::_('MOD_MENU_IMPORTANT_ITEM_MODULE_MANAGER');
			}

			if ($rContainer)
			{
				$missing[] = JText::_('MOD_MENU_IMPORTANT_ITEM_COMPONENTS_CONTAINER');
			}

			$uri = clone Uri::getInstance();
			$uri->setVar('recover_menu', 1);

			$table    = Table::getInstance('MenuType');
			$menutype = $params->get('menutype');

			$table->load(array('menutype' => $menutype));

			$menutype = $table->get('title', $menutype);
			$message  = JText::sprintf('MOD_MENU_IMPORTANT_ITEMS_INACCESSIBLE_LIST_WARNING', $menutype, implode(', ', $missing), $uri);

			$app->enqueueMessage($message, 'warning');
		}

		return false;
	}

	/**
	 * Filter and perform other preparatory tasks for loaded menu items based on access rights and module configurations for display
	 *
	 * @param   \stdClass[]  $items  The levelled array of menu item objects
	 *
	 * @return  array
	 *
	 * @since   3.8.0
	 */
	protected function preprocess($items)
	{
		$result     = array();
		$user       = Factory::getUser();
		$authLevels = $user->getAuthorisedViewLevels();
		$language   = Factory::getLanguage();

		$noSeparator = true;

		foreach ($items as $i => &$item)
		{
			// Exclude item with menu item option set to exclude from menu modules
			if ($item->params->get('menu_show', 1) == 0)
			{
				continue;
			}

			$item->scope = isset($item->scope) ? $item->scope : 'default';
			$item->icon  = isset($item->icon) ? $item->icon : '';

			// Whether this scope can be displayed. Applies only to preset items. Db driven items should use un/published state.
			if (($item->scope == 'help' && !$this->params->get('showhelp')) || ($item->scope == 'edit' && !$this->params->get('shownew')))
			{
				continue;
			}

			// Exclude item if the component is not authorised
			$assetName = $item->element;

			if ($item->element == 'com_categories')
			{
				parse_str($item->link, $query);
				$assetName = isset($query['extension']) ? $query['extension'] : 'com_content';
			}
			elseif ($item->element == 'com_fields')
			{
				parse_str($item->link, $query);
				list($assetName) = isset($query['context']) ? explode('.', $query['context'], 2) : array('com_fields');
			}

			if ($assetName && !$user->authorise(($item->scope == 'edit') ? 'core.create' : 'core.manage', $assetName))
			{
				continue;
			}

			// Exclude if menu item set access level is not met
			if ($item->access && !in_array($item->access, $authLevels))
			{
				continue;
			}

			// Exclude if link is invalid
			if (!in_array($item->type, array('separator', 'heading', 'container')) && trim($item->link) == '')
			{
				continue;
			}

			// Process any children if exists
			$item->submenu = $this->preprocess($item->submenu);

			// Populate automatic children for container items
			if ($item->type == 'container')
			{
				$exclude    = (array) $item->params->get('hideitems') ?: array();
				$components = MenusHelper::getMenuItems('main', false, $exclude);

				$item->components = MenuHelper::createLevels($components);
				$item->components = $this->preprocess($item->components);
				$item->components = ArrayHelper::sortObjects($item->components, 'text', 1, false, true);
			}

			// Exclude if there are no child items under heading or container
			if (in_array($item->type, array('heading', 'container')) && empty($item->submenu) && empty($item->components))
			{
				continue;
			}

			// Remove repeated and edge positioned separators, It is important to put this check at the end of any logical filtering.
			if ($item->type == 'separator')
			{
				if ($noSeparator)
				{
					continue;
				}

				$noSeparator = true;
			}
			else
			{
				$noSeparator = false;
			}

			// Ok we passed everything, load language at last only
			if ($item->element)
			{
				$language->load($item->element . '.sys', JPATH_ADMINISTRATOR, null, false, true) ||
				$language->load($item->element . '.sys', JPATH_ADMINISTRATOR . '/components/' . $item->element, null, false, true);
			}

			$item->text = JText::_($item->title);

			$result[$i] = $item;
		}

		// If last one was a separator remove it too.
		if ($noSeparator && isset($i))
		{
			unset($result[$i]);
		}

		return $result;
	}

	/**
	 * Load the menu items from a hierarchical list of items into the menu tree
	 *
	 * @param   \stdClass[]  $levels  Menu items as a hierarchical list format
	 *
	 * @return  void
	 *
	 * @since   3.8.0
	 */
	protected function populateTree($levels)
	{
		foreach ($levels as $item)
		{
			$class = $this->enabled ? $item->class : 'disabled';

			if ($item->type == 'separator')
			{
				$this->tree->addChild(new Node\Separator($item->title));
			}
			elseif ($item->type == 'heading')
			{
				// We already excluded heading type menu item with no children.
				$this->tree->addChild(new Node\Heading($item->title, $class, null, $item->icon), $this->enabled);

				if ($this->enabled)
				{
					$this->populateTree($item->submenu);
					$this->tree->getParent();
				}
			}
			elseif ($item->type == 'url')
			{
				$cNode = new Node\Url($item->title, $item->link, $item->browserNav, $class, null, $item->icon);
				$this->tree->addChild($cNode, $this->enabled);

				if ($this->enabled)
				{
					$this->populateTree($item->submenu);
					$this->tree->getParent();
				}
			}
			elseif ($item->type == 'component')
			{
				$cNode = new Node\Component($item->title, $item->element, $item->link, $item->browserNav, $class, null, $item->icon);
				$this->tree->addChild($cNode, $this->enabled);

				if ($this->enabled)
				{
					$this->populateTree($item->submenu);
					$this->tree->getParent();
				}
			}
			elseif ($item->type == 'container')
			{
				// We already excluded container type menu item with no children.
				$this->tree->addChild(new Node\Container($item->title, $item->class, null, $item->icon), $this->enabled);

				if ($this->enabled)
				{
					$this->populateTree($item->submenu);

					// Add a separator between dynamic menu items and components menu items
					if (count($item->submenu) && count($item->components))
					{
						$this->tree->addChild(new Node\Separator);
					}

					$this->populateTree($item->components);

					$this->tree->getParent();
				}
			}
		}
	}
}
