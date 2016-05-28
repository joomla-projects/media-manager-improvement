<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<form
	action="<?php echo JUri::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken(); ?>=1&amp;format=html"
	id="uploadForm" class="form-inline" name="uploadForm" method="post" enctype="multipart/form-data">
	<div id="uploadform">
		<fieldset id="upload-noflash" class="actions">
			<label for="upload-file"
			       class="control-label"><?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?></label>
			<input type="file" id="upload-file" name="Filedata[]" multiple/>
			<button class="btn btn-primary" id="upload-submit"><span
					class="icon-upload icon-white"></span> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>
			</button>
			<p class="help-block"><?php echo $this->config->get('upload_maxsize') == '0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></p>
		</fieldset>
		<input class="update-folder" type="hidden" name="folder" id="folder"
		       value="<?php echo $this->state->folder; ?>"/>
		<?php $this->session->set('com_media.return_url', 'index.php?option=com_media'); ?>
	</div>
</form>
