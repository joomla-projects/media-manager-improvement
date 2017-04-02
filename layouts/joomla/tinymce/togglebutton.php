<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$name = $displayData;

?>
<div class="toggle-editor btn-toolbar float-right clearfix mt-3">
	<div class="btn-group">
		<a class="btn btn-secondary" href="#"
			onclick="tinyMCE.execCommand('mceToggleEditor', false, '<?php echo $name; ?>');return false;"
			title="<?php echo JText::_('PLG_TINY_BUTTON_TOGGLE_EDITOR'); ?>"
		>
			<span class="icon-eye"></span> <?php echo JText::_('PLG_TINY_BUTTON_TOGGLE_EDITOR'); ?>
		</a>
	</div>
</div>