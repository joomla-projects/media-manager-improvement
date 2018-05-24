<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Make thing clear
 *
 * @var JForm   $tmpl             The Empty form for template
 * @var array   $forms            Array of JForm instances for render the rows
 * @var bool    $multiple         The multiple state for the form field
 * @var int     $min              Count of minimum repeating in multiple mode
 * @var int     $max              Count of maximum repeating in multiple mode
 * @var string  $name             Name of the input field.
 * @var string  $fieldname        The field name
 * @var string  $control          The forms control
 * @var string  $label            The field label
 * @var string  $description      The field description
 * @var array   $buttons          Array of the buttons that will be rendered
 * @var bool    $groupByFieldset  Whether group the subform fields by it`s fieldset
 */
extract($displayData);

// Add script
if ($multiple)
{
	HTMLHelper::_('webcomponent', 'system/webcomponents/joomla-field-subform.min.js', ['relative' => true, 'version' => 'auto', 'detectBrowser' => false, 'detectDebug' => true]);
}
$sublayout = empty($groupByFieldset) ? 'section' : 'section-byfieldsets';
?>

	<div class="subform-repeatable-wrapper subform-layout">
		<joomla-field-subform class="subform-repeatable" name="<?php echo $name; ?>"
			button-add=".group-add" button-remove=".group-remove" button-move="<?php echo empty($buttons['move']) ? '' : '.group-move' ?>"
			repeatable-element=".subform-repeatable-group" minimum="<?php echo $min; ?>" maximum="<?php echo $max; ?>">
			<?php if (!empty($buttons['add'])) : ?>
			<div class="btn-toolbar">
				<div class="btn-group">
					<a class="group-add btn btn-sm button btn-success" aria-label="<?php echo Text::_('JGLOBAL_FIELD_ADD'); ?>" tabindex="0">
						<span class="fa fa-plus icon-white" aria-hidden="true"></span> </a>
				</div>
			</div>
			<?php endif; ?>
		<?php
		foreach ($forms as $k => $form) :
			echo $this->sublayout($sublayout, array('form' => $form, 'basegroup' => $fieldname, 'group' => $fieldname . $k, 'buttons' => $buttons));
		endforeach;
		?>
		<?php if ($multiple) : ?>
		<template class="subform-repeatable-template-section" style="display: none;"><?php
			echo trim($this->sublayout($sublayout, array('form' => $tmpl, 'basegroup' => $fieldname, 'group' => $fieldname . 'X', 'buttons' => $buttons)));
		?></template>
		<?php endif; ?>
		</joomla-field-subform>
	</div>

