<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Menu\MenuHelper;

JFormHelper::loadFieldClass('list');

/**
 * Administrator Menu Presets list field.
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldMenuPreset extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected $type = 'MenuPreset';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$options = array();
		$presets = MenuHelper::getPresets();

		foreach ($presets as $preset)
		{
			$options[] = JHtml::_('select.option', $preset->name, JText::_($preset->title));
		}

		return array_merge(parent::getOptions(), $options);
	}
}
