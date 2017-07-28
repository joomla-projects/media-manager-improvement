<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Templates\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\Component\Templates\Administrator\Helper\TemplatesHelper;

FormHelper::loadFieldClass('list');

/**
 * Template Location field.
 *
 * @since  3.5
 */
class TemplatelocationField extends \JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var	   string
	 * @since  3.5
	 */
	protected $type = 'TemplateLocation';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.5
	 */
	public function getOptions()
	{
		$options = TemplatesHelper::getClientOptions();

		return array_merge(parent::getOptions(), $options);
	}
}
