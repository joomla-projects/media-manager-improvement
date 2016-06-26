<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$folder     = $this->getFolder();
$folderData = $folder['data'];

// @todo: Add proper CSS classes for styling
// @todo: Add vertical and horizontal alignment of preview
?>
<li class="imgOutline thumbnail height-80 width-80 center" style="position:relative;">
	<?php if ($this->user->authorise('core.delete', 'com_media')): ?>
		<div class="tasks-right" style="position:absolute; right:0; margin-right:2px;">
			<a class="delete-item" target="_top"
			   href="index.php?option=com_media&amp;task=folder.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;rm[]=<?php echo $folderData->relative; ?>"
			   rel="<?php echo $folderData->relative; ?>' :: <?php echo $folderData->subfiles + $folderData->subfolders; ?>">
                <span class="icon-remove small hasTooltip"
                      title="<?php echo JHtml::tooltipText('JACTION_DELETE'); ?>"></span>
			</a>
		</div>
		<div class="tasks-left" style="position:absolute; left:0; margin-left:5px;">
			<input type="checkbox" name="rm[]" value="<?php echo $folderData->name; ?>"/>
		</div>
	<?php endif; ?>
	<a href="#" data-href="<?php echo $folderData->relative; ?>" class="ajaxInit"
	   title="<?php echo $folderData->relative; ?>">
		<div class="height-50">
			<span class="icon-folder-2"></span>
		</div>
		<div class="small">
			<?php echo $folderData->name; ?>
		</div>
	</a>
</li>
