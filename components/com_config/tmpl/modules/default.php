<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.combobox');

HTMLHelper::_('script', 'com_config/modules-default.js', ['relative' => true, 'version' => 'auto']);

$hasContent = empty($this->item['module']) || $this->item['module'] === 'custom' || $this->item['module'] === 'mod_custom';

// If multi-language site, make language read-only
if (JLanguageMultilang::isEnabled())
{
	$this->form->setFieldAttribute('language', 'readonly', 'true');
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_config'); ?>" method="post" name="adminForm" id="modules-form" class="form-validate"  data-cancel-task="config.cancel.modules">
	<div class="row">
		<div class="col-md-12">

			<div class="btn-toolbar" role="toolbar" aria-label="<?php echo JText::_('JTOOLBAR'); ?>">
				<div class="btn-group mr-2">
					<button type="button" class="btn btn-primary" data-submit-task="modules.apply">
						<span class="fa fa-check" aria-hidden="true"></span>
						<?php echo JText::_('JAPPLY') ?>
					</button>
				</div>
				<div class="btn-group mr-2">
					<button type="button" class="btn btn-secondary" data-submit-task="modules.save">
						<span class="fa fa-check" aria-hidden="true"></span>
						<?php echo JText::_('JSAVE') ?>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" class="btn btn-danger" data-submit-task="modules.cancel">
						<span class="fa fa-times" aria-hidden="true"></span>
						<?php echo JText::_('JCANCEL') ?>
					</button>
				</div>
			</div>

			<hr>

			<legend><?php echo JText::_('COM_CONFIG_MODULES_SETTINGS_TITLE'); ?></legend>

			<div>
				<?php echo JText::_('COM_CONFIG_MODULES_MODULE_NAME'); ?>
				<span class="badge badge-secondary"><?php echo $this->item['title']; ?></span>
				&nbsp;&nbsp;
				<?php echo JText::_('COM_CONFIG_MODULES_MODULE_TYPE'); ?>
				<span class="badge badge-secondary"><?php echo $this->item['module']; ?></span>
			</div>
			<hr>

			<div class="row">
				<div class="col-md-12">

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('title'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('title'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('showtitle'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('showtitle'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('position'); ?>
						</div>
						<div class="controls">
							<?php echo $this->loadTemplate('positions'); ?>
						</div>
					</div>

					<hr>

					<?php if (JFactory::getUser()->authorise('core.edit.state', 'com_modules.module.' . $this->item['id'])) : ?>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('published'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('published'); ?>
						</div>
					</div>
					<?php endif ?>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('publish_up'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('publish_up'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('publish_down'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('publish_down'); ?>
						</div>
					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('access'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('access'); ?>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('ordering'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('ordering'); ?>
						</div>
					</div>

					<?php if (\JLanguageMultilang::isEnabled()) : ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('language'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('language'); ?>
							</div>
						</div>
					<?php endif; ?>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('note'); ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('note'); ?>
						</div>
					</div>

					<hr>

					<div id="options">
						<?php echo $this->loadTemplate('options'); ?>
					</div>

					<?php if ($hasContent) : ?>
						<div class="tab-pane" id="custom">
							<?php echo $this->form->getInput('content'); ?>
						</div>
					<?php endif; ?>
				</div>

				<input type="hidden" name="id" value="<?php echo $this->item['id']; ?>">
				<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return', null, 'base64'); ?>">
				<input type="hidden" name="task" value="">
				<?php echo JHtml::_('form.token'); ?>

			</div>

		</div>
	</div>
</form>
