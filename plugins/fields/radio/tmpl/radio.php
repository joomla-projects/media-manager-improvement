<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Radio
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$value = $field->value;

if ($value == '')
{
	return;
}

$value   = (array) $value;
$texts   = array();
$options = $this->getOptionsFromField($field);

foreach ($options as $optionValue => $optionText)
{
	if (in_array((string) $optionValue, $value))
	{
		$texts[] = Text::_($optionText);
	}
}


echo htmlentities(implode(', ', $texts));
