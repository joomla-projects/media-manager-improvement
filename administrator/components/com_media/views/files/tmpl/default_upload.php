<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

if ($this->canUpload):?>
<div id="collapseUpload">
	<form action="<?php echo JUri::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component&amp;<?php echo JFactory::getSession()->getName() . '=' . JFactory::getSession()->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;format=html" id="uploadForm" class="form-inline" name="uploadForm" method="post" enctype="multipart/form-data">
		<div id="uploadform">
			<fieldset id="upload-noflash" class="actions">
					<label for="upload-file" class="control-label"><?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?></label>
						<input required type="file" id="upload-file" name="Filedata[]" multiple /> <button class="btn btn-primary" id="upload-submit"><span class="icon-upload icon-white"></span> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></button>
					<p class="help-block">
						<?php $cMax    = (int) JComponentHelper::getParams('com_media')->get('upload_maxsize'); ?>
						<?php $maxSize = JUtility::getMaxUploadSize($cMax . 'MB'); ?>
						<?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', JHtml::_('number.bytes', $maxSize)); ?>
					</p>
			</fieldset>
			<input class="update-category" type="hidden" name="category" id="category" value="<?php echo $this->state->get('filter.category_id'); ?>" />
			<?php JFactory::getSession()->set('com_media.return_url', 'index.php?option=com_media'); ?>
		</div>
	</form>
</div>
<?php endif;?>