<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Imagelist
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$value = $field->value;
$class = $fieldParams->get('image_class');

if ($class)
{
	$class = ' class="' . $class . '"';
}

if ($value == '')
{
	return;
}

$value  = (array) $value;
$buffer = '';

foreach ($value as $path)
{
	if (!$path || $path == '-1')
	{
		continue;
	}

	$buffer .= '<img src="images/' . $fieldParams->get('directory', '/')
				. '/' . htmlentities($path) . '"' . $class . '>';
}

echo $buffer;
