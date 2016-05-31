<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

$file       = $this->_tmp_file;
$params     = new Registry;
$dispatcher = JEventDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_media.file', &$file, &$params));

// @todo: Add proper CSS classes for styling
// @todo: Add vertical and horizontal alignment of preview
?>
	<li class="imgOutline thumbnail height-80 width-80 center" style="position:relative;">
		<?php if ($this->user->authorise('core.delete', 'com_media')): ?>
			<div class="tasks-right" style="position:absolute; right:0;">
				<a class="delete-item" target="_top"
				   href="index.php?option=com_media&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;rm[]=<?php echo $file->path_relative; ?>"
				   rel="<?php echo $file->path_relative; ?>">
                <span class="icon-remove small hasTooltip"
                      title="<?php echo JHtml::tooltipText('JACTION_DELETE'); ?>"></span>
				</a>
			</div>
			<div class="tasks-left" style="position:absolute; left:0;">
				<input type="checkbox" name="rm[]" value="<?php echo $file->name; ?>"/>
			</div>
		<?php endif; ?>
		<a class="img-preview"
		   href="index.php?option=com_media&view=file&file=<?php echo $file->path_relative; ?>"
			<?php /* href="javascript:ImageManager.populateFields('<?php echo $file->path_relative; ?>')" */ ?>
           title="<?php echo $file->name; ?>">
			<div class="thumbnail height-50">
				<?php $layoutFile = 'file.thumb_' . $file->file_type; ?>
				<?php $layout = new JLayoutFile($layoutFile); ?>
				<?php echo $layout->render(array('file' => $file)); ?>
			</div>
			<div class="filename small">
				<?php echo JHtml::_('string.truncate', $file->name, 10, false); ?>
			</div>
		</a>
	</li>
<?php $dispatcher->trigger('onContentAfterDisplay', array('com_media.file', &$file, &$params));
