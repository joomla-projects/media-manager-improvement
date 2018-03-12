<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Form\Field;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Layout\LayoutHelper;

/**
 * Form Field class for the Joomla Platform.
 * Field for assigning permissions to groups for a given asset
 *
 * @see    JAccess
 * @since  11.1
 */
class RulesField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Rules';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $layout = 'joomla.form.field.rules.tabs';

	/**
	 * The section.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $section;

	/**
	 * The component.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $component;

	/**
	 * The assetField.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $assetField;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to get the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'section':
			case 'component':
			case 'assetField':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to set the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'section':
			case 'component':
			case 'assetField':
				$this->$name = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value. This acts as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     FormField::setup()
	 * @since   3.2
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->section    = $this->element['section'] ? (string) $this->element['section'] : '';
			$this->component  = $this->element['component'] ? (string) $this->element['component'] : '';
			$this->assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
		}

		return $return;
	}

	/**
	 * Method to get the field input markup for Access Control Lists.
	 * Optionally can be associated with a specific component and section.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 * @todo:   Add access check.
	 */
	protected function getInput()
	{
		// Initialise some field attributes.
		$section    = $this->section;
		$assetField = $this->assetField;
		$component  = empty($this->component) ? 'root.1' : $this->component;

		// Current view is global config?
		$this->isGlobalConfig = $component === 'root.1';

		// Get the actions for the asset.
		$this->actions = Access::getActions($component, $section);

		// Iterate over the children and add to the actions.
		foreach ($this->element->children() as $el)
		{
			if ($el->getName() == 'action')
			{
				$this->actions[] = (object) array(
					'name' => (string) $el['name'],
					'title' => (string) $el['title'],
					'description' => (string) $el['description'],
				);
			}
		}

		// Get the asset id.
		// Note that for global configuration, com_config injects asset_id = 1 into the form.
		$this->assetId       = $this->form->getValue($assetField);
		$this->newItem       = empty($assetId) && $this->isGlobalConfig === false && $section !== 'component';
		$parentAssetId = null;

		// If the asset id is empty (component or new item).
		if (empty($assetId))
		{
			// Get the component asset id as fallback.
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('name') . ' = ' . $db->quote($component));

			$db->setQuery($query);

			$assetId = (int) $db->loadResult();

			/**
			 * @to do: incorrect info
			 * When creating a new item (not saving) it uses the calculated permissions from the component (item <-> component <-> global config).
			 * But if we have a section too (item <-> section(s) <-> component <-> global config) this is not correct.
			 * Also, currently it uses the component permission, but should use the calculated permissions for achild of the component/section.
			 */
		}

		// If not in global config we need the parent_id asset to calculate permissions.
		if (!$this->isGlobalConfig)
		{
			// In this case we need to get the component rules too.
			$db = Factory::getDbo();

			$query = $db->getQuery(true)
				->select($db->quoteName('parent_id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('id') . ' = ' . $assetId);

			$db->setQuery($query);

			$this->parentAssetId = (int) $db->loadResult();
		}
		
		// Get the rules for just this asset (non-recursive).
		$this->assetRules = Access::getAssetRules($assetId, false, false);

		// Get the available user groups.
		$this->groups = $this->getUserGroups();

		// Trim the trailing line in the layout file
		return trim($this->getRenderer($this->layout)->render($this->getLayoutData()));
	}

	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$extraData = array(
			'groups'  		=>	$this->groups,
			'section'		=>	$this->section,
			'actions'		=>	$this->actions,
			'assetId'		=>	$this->assetId,
			'newItem'		=>	$this->newItem,
			'assetRules'		=>	$this->assetRules,
			'isGlobalConfig'	=>	$this->isGlobalConfig,
			'parentAssetId'		=> 	$this->parentAssetId,
		);

		return array_merge($data, $extraData);
	}

	/**
	 * Get a list of the user groups.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	protected function getUserGroups()
	{
		$options = UserGroupsHelper::getInstance()->getAll();

		foreach ($options as &$option)
		{
			$option->value = $option->id;
			$option->text  = $option->title;
		}

		return array_values($options);
	}
}
