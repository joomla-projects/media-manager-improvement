<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$input = JFactory::getApplication()->input;
?>
<div id="#template-manager-folder" class="container-fluid">
	<div class="mt-2 p-2">
		<div class="row">
			<div class="col-md-6">
				<div class="tree-holder">
					<?php echo $this->loadTemplate('folders'); ?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card card-outline-secondary mb-2">
					<div class="card-block">
						<form method="post" action="<?php echo JRoute::_('index.php?option=com_templates&task=template.createFolder&id=' . $input->getInt('id') . '&file=' . $this->file); ?>">
							<div class="form-group">
								<label><?php echo JText::_('COM_TEMPLATES_FOLDER_NAME'); ?></label>
								<input type="text" name="name" class="form-control" required>
								<input type="hidden" class="address" name="address">
								<?php echo JHtml::_('form.token'); ?>
							</div>
							<button type="submit" class="btn btn-primary"><?php echo JText::_('COM_TEMPLATES_BUTTON_CREATE'); ?></button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
