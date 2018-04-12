<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData, null);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellchec       Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 * @var   array    $checked         Is this field checked?
 * @var   array    $position        Is this field checked?
 * @var   array    $control         Is this field checked?
 * @var   array    $colors          The specified colors
 */

$class    = ' class="custom-select ' . trim($class) . '"';
$disabled = $disabled ? ' disabled' : '';
$readonly = $readonly ? ' readonly' : '';

HTMLHelper::_('webcomponent', 'system/webcomponents/joomla-field-simple-color.min.js', ['version' => 'auto', 'relative' => true]);
?>
<joomla-field-simple-color text-select="<?php echo Text::_('JFIELD_COLOR_SELECT'); ?>" text-color="<?php echo Text::_('JFIELD_COLOR_VALUE'); ?>" text-close="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>" text-transparent="<?php echo Text::_('JFIELD_COLOR_TRANSPARENT'); ?>">
	<select name="<?php echo $name; ?>" id="<?php echo $id; ?>"<?php
	echo $disabled; ?><?php echo $readonly; ?><?php echo $required; ?><?php echo $class; ?><?php echo $position; ?><?php
	echo $onchange; ?><?php echo $autofocus; ?> style="visibility:hidden;width:22px;height:1px">
		<?php foreach ($colors as $i => $c) : ?>
			<option<?php echo ($c === $color ? ' selected="selected"' : ''); ?> value="<?php echo $c; ?>"></option>
		<?php endforeach; ?>
	</select>
</joomla-field-simple-color>
